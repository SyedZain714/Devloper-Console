================================================================================
                     SuiteCRM Developer Console v1.0.0
================================================================================

DESCRIPTION
-----------
Developer Console is an integrated code editor for SuiteCRM that allows 
administrators to browse and edit files directly from the admin panel.

FEATURES
--------
- File browser with tree view navigation
- Syntax highlighting for PHP, JavaScript, CSS, HTML, JSON, XML, SQL, and more
- Auto-detection of file type and syntax mode
- Search/filter files by name
- Keyboard shortcuts (Ctrl+S to save)
- Unsaved changes detection and warning
- Dark theme IDE-style interface
- Security restricted to admin users only
- Can only save files in the custom/ directory

REQUIREMENTS
------------
- SuiteCRM 7.x or 8.x
- PHP 7.2 or higher
- Admin user access

INSTALLATION
------------
1. Go to Admin > Module Loader
2. Upload the DeveloperConsole.zip file
3. Click Install
4. After installation, go to Admin > Repair > Quick Repair and Rebuild
5. The Developer Console will appear in Admin panel under "Developer Tools"

USAGE
-----
1. Navigate to Admin panel
2. Find "Developer Tools" section
3. Click "Developer Console"
4. Browse files in the left sidebar
5. Click on a file to open it in the editor
6. Edit the file and press Ctrl+S or click Save button

SECURITY NOTES
--------------
- Only admin users can access the Developer Console
- Files can only be saved in the custom/ directory for safety
- The console prevents directory traversal attacks
- Large files (>2MB) are blocked for performance

UNINSTALLATION
--------------
1. Go to Admin > Module Loader
2. Find Developer Console in the installed modules list
3. Click Uninstall

SUPPORT
-------
For issues or feature requests, please contact your SuiteCRM administrator.

LICENSE
-------
This module is provided as-is for use with SuiteCRM.

================================================================================

