 <?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: upload.php
 * Description:
 */
?>

 <?php include 'header.php'; ?>

 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

 <h2>Upload Media or Lost & Found</h2>

 <form id="uploadForm" method="post" enctype="multipart/form-data" action="/media/upload">
     <label for="media_category">Upload Type:</label>
     <select name="media_category" id="media_category" required>
         <option value="regular" selected>Regular Media (Images & Videos)</option>
         <option value="lostfound">Lost & Found (Images Only)</option>
     </select>
     <br><br>

     <label for="event_id">Event:</label>
     <select name="event_id" id="event_id" required>
         <option value="">-- Select an Event --</option>
         <?php foreach ($events as $event): ?>
             <option value="<?= htmlspecialchars($event['event_id']) ?>">
                 <?= htmlspecialchars($event['title']) ?> (<?= htmlspecialchars($event['event_date']) ?>)
             </option>
         <?php endforeach; ?>
     </select>
     <br><br>

     <label for="media_file">Select File:</label>
     <input type="file" name="media_file" id="media_file" accept="image/*,video/*" required>
     <br><br>

     <label for="tags">Tags (genre, scene, activity):</label>
     <select name="tags[]" id="tags" multiple style="width: 100%;">
         <?php foreach ($tags as $tag): ?>
             <option value="<?= htmlspecialchars($tag['tag_id']) ?>">
                 <?= htmlspecialchars($tag['name']) ?> (<?= htmlspecialchars($tag['tag_type']) ?>)
             </option>
         <?php endforeach; ?>
     </select>
     <br><br>

     <button type="submit">Upload</button>
 </form>

 <div id="uploadResult"></div>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <script src="/js/upload_media.js"></script>

 <?php include 'footer.php'; ?>
