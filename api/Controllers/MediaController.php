<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: MediaController.php
 * Description:
 */


namespace api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use api\Authentication\SessionManager;
use api\Models\Media;
use api\Models\Tag;

class MediaController
{
    // handle media upload (regular media or lost & found)
    public function uploadMedia(Request $request, Response $response): Response
    {
        file_put_contents(__DIR__ . '/../../log.txt',
            "UPLOAD DEBUG:\n" .
            print_r($_POST, true) .
            print_r($_FILES, true) .
            "\n\n",
            FILE_APPEND
        );

        $userId = SessionManager::getUserId();
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        // require media file
        if (!isset($files['media_file'])) {
            $response->getBody()->write(json_encode(['error' => 'No media file uploaded']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $mediaFile = $files['media_file'];

        // check for upload errors
        if ($mediaFile->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(['error' => 'Upload error code: ' . $mediaFile->getError()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $mediaCategory = $data['media_category'] ?? 'regular';
        $eventId = isset($data['event_id']) ? (int)$data['event_id'] : null;

        // require an event ID
        if (!$eventId) {
            $response->getBody()->write(json_encode(['error' => 'Event ID is required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // check file type
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/quicktime'];
        $fileType = $mediaFile->getClientMediaType();

        if ($mediaCategory === 'lostfound') {
            if (!in_array($fileType, $allowedImageTypes)) {
                $response->getBody()->write(json_encode(['error' => 'Lost & Found uploads accept only images']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        } else {
            if (!in_array($fileType, array_merge($allowedImageTypes, $allowedVideoTypes))) {
                $response->getBody()->write(json_encode(['error' => 'Invalid media type']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        // save the file to the uploads folder
        $uploadDir = __DIR__ . '/../../uploads/';
        $filename = bin2hex(random_bytes(16)) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $mediaFile->getClientFilename());
        $filepath = $uploadDir . $filename;

        try {
            $mediaFile->moveTo($filepath);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Failed to save uploaded file']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        // figure out what type of media it is
        if ($mediaCategory === 'lostfound') {
            $mediaType = 'lost_found';
        } elseif (in_array($fileType, $allowedImageTypes)) {
            $mediaType = 'image';
        } else {
            $mediaType = 'video';
        }

        // insert media into database
        $mediaId = Media::insertMedia([
            'user_id' => $userId,
            'event_id' => $eventId,
            'filepath' => '/uploads/' . $filename,
            'media_type' => $mediaType,
            'media_category' => $mediaCategory,
            'approved' => 0,
            'is_flagged_ai' => 0,
        ]);

        if (!$mediaId) {
            $response->getBody()->write(json_encode(['error' => 'Failed to save media record']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }

        // handle tags if regular media
        if ($mediaCategory === 'regular') {
            $tags = $data['tags'] ?? [];
            if (!is_array($tags)) {
                $tags = [];
            }

            $validTagIds = Tag::getValidTagIds($tags);
            foreach ($validTagIds as $tagId) {
                Media::addTagToMedia($mediaId, $tagId);
            }
        }

        // run AI check on regular media only
        if ($mediaCategory === 'regular') {
            $cmd = escapeshellcmd("python3 " . __DIR__ . "/../../ai/check_ai.py " . escapeshellarg($filepath));
            $output = shell_exec($cmd);

            $isFlagged = false;
            if ($output) {
                $result = json_decode($output, true);
                if (!empty($result['flagged'])) {
                    $isFlagged = true;
                }
            }

            Media::updateAIFlag($mediaId, $isFlagged ? 1 : 0);
        }

        $response->getBody()->write(json_encode(['success' => true, 'media_id' => $mediaId]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // return a list of approved media (used on public-facing pages)
    public function listApprovedMedia(Request $request, Response $response): Response
    {
        $media = Media::getApprovedMedia();
        $response->getBody()->write(json_encode($media));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // show the upload page with event and tag dropdowns
    public function viewUploadPage(Request $request, Response $response): Response
    {
        $events = \api\Models\AdminModel::listEvents();
        $tags = \api\Models\Tag::getAllTags();

        ob_start();
        include __DIR__ . '/../../app/upload.php';
        $output = ob_get_clean();

        $response->getBody()->write($output);
        return $response->withHeader('Content-Type', 'text/html');
    }

    // search media based on event, date, tags, and archive state
    public function search(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();

        $eventTitle = $queryParams['event'] ?? null;
        $eventDate = $queryParams['date'] ?? null;
        $tagIds = isset($queryParams['tags']) ? (array)$queryParams['tags'] : [];
        $includeArchived = !empty($queryParams['include_archived']) && $queryParams['include_archived'] === 'true';

        $results = Media::search($eventTitle, $eventDate, $tagIds, $includeArchived);

        $response->getBody()->write(json_encode($results));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
