api
    Authentication
        SessionManager
    Controllers
        AuthController
        MediaController
            Upload flow: checks user, event, file validity, media category
            Lost & found restricts uploads to images only
            Saves file safely with random prefix, inserts DB record unapproved, AI flag off by default
            Runs AI detection (via Python script) only on regular media and updates AI flag
            Returns JSON success or error
            Lists approved media as JSON for browsing
    Middleware
        RequireAuth
    Models
        Favorite
        InviteCode
        Media
            getPDO() centralized for DB connection (consistent with AdminModel)
            insertMedia() inserts new record and returns media_id
            updateAIFlag() updates AI flag column after AI check
            getPendingMedia(), approveMedia(), rejectMedia() support admin moderation logic (same as AdminModel but here centralized)
            getApprovedMedia() fetches all approved media for user display
        Session
        User- no getPDO because only 2 methods

    Validation
        InputValidator
app
    dashboard
    footer
    header
    lostandfound
    main
    pending
    signin
    singup
    upload
config
    bootstrap
    config
    dependencies
    routes
    services
public
    css
        style.css
    img
        images for project
    js
        admin
        index
        message
        signin
        signout
        signup
    index
scripts
    check_ai.py
        experimental, low accuracy, gonna keep it for now anyways
    hash_password
    verify_password
src
tools
    hash_signup

Notes
Database uses direct PDO set up per controller
password hash python
email hash php
stripes expires in 90 days



