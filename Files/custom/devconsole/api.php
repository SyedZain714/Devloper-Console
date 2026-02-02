<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $current_user;

// Admin-only access
if (!is_admin($current_user)) {
    http_response_code(403);
    die('Unauthorized - Admin access required');
}

$action = $_GET['action'] ?? '';
$basePath = realpath(dirname(__FILE__) . '/../../');

// Directories to skip (for performance and security reasons)
$skipDirs = [
    'vendor',
    'node_modules', 
    '.git',
    '.svn',
    'cache/smarty',
    'cache/themes',
    'cache/modules',
    'cache/jsLanguage',
    'upload',
    'logs'
];

// File extensions to show
$allowedExts = [
    'php', 'js', 'css', 'html', 'htm', 'tpl', 'json', 'xml', 
    'sql', 'md', 'txt', 'ini', 'yml', 'yaml', 'htaccess', 'log'
];

function shouldSkipDir($path, $basePath, $skipDirs) {
    $relativePath = str_replace($basePath . '/', '', $path);
    foreach ($skipDirs as $skip) {
        if ($relativePath === $skip || strpos($relativePath, $skip . '/') === 0) {
            return true;
        }
    }
    // Skip hidden directories (starting with .)
    $dirName = basename($path);
    if ($dirName !== '.' && $dirName !== '..' && strpos($dirName, '.') === 0) {
        return true;
    }
    return false;
}

function listFilesRecursive($dir, $basePath, $skipDirs, $allowedExts, $depth = 0) {
    // Limit depth to prevent infinite recursion and performance issues
    if ($depth > 10 || !is_dir($dir) || !is_readable($dir)) {
        return [];
    }
    
    if (shouldSkipDir($dir, $basePath, $skipDirs)) {
        return [];
    }
    
    $result = [];
    $items = @scandir($dir);
    
    if ($items === false) {
        return [];
    }
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $fullPath = $dir . '/' . $item;
        
        if (is_dir($fullPath)) {
            if (!shouldSkipDir($fullPath, $basePath, $skipDirs)) {
                $children = listFilesRecursive($fullPath, $basePath, $skipDirs, $allowedExts, $depth + 1);
                if (!empty($children)) {
                    $result[$item] = $children;
                }
            }
        } else {
            // Check file extension
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts) || $item === '.htaccess') {
                $result[$item] = null; // null indicates it's a file
            }
        }
    }
    
    return $result;
}

function isPathSafe($path, $basePath) {
    $realPath = realpath($path);
    if ($realPath === false) {
        // For new files, check parent directory
        $parentDir = dirname($path);
        $realParent = realpath($parentDir);
        if ($realParent === false) {
            return false;
        }
        return strpos($realParent, $basePath) === 0;
    }
    return strpos($realPath, $basePath) === 0;
}

switch ($action) {
    case 'list':
        header('Content-Type: application/json; charset=utf-8');
        
        $tree = listFilesRecursive($basePath, $basePath, $skipDirs, $allowedExts);
        echo json_encode($tree, JSON_PRETTY_PRINT);
        break;
        
    case 'open':
        $path = $_GET['path'] ?? '';
        
        // Security: prevent directory traversal
        if (strpos($path, '..') !== false) {
            http_response_code(403);
            die('Invalid path');
        }
        
        $fullPath = $basePath . '/' . $path;
        
        if (!isPathSafe($fullPath, $basePath)) {
            http_response_code(403);
            die('Access denied');
        }
        
        if (!file_exists($fullPath)) {
            http_response_code(404);
            die('File not found');
        }
        
        if (!is_readable($fullPath)) {
            http_response_code(403);
            die('File not readable');
        }
        
        if (is_dir($fullPath)) {
            http_response_code(400);
            die('Cannot open directory');
        }
        
        // Check file size (limit to 2MB)
        if (filesize($fullPath) > 2 * 1024 * 1024) {
            http_response_code(413);
            die('File too large (max 2MB)');
        }
        
        header('Content-Type: text/plain; charset=utf-8');
        echo file_get_contents($fullPath);
        break;
        
    case 'save':
        $path = $_POST['path'] ?? '';
        $content = $_POST['content'] ?? '';
        
        // Decode HTML entities that may have been encoded by SuiteCRM's input sanitization
        // This fixes issues with PHP tags like <?php becoming &lt;?php
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Security: prevent directory traversal
        if (strpos($path, '..') !== false) {
            http_response_code(403);
            die('Invalid path');
        }
        
        $fullPath = $basePath . '/' . $path;
        
        if (!isPathSafe($fullPath, $basePath)) {
            http_response_code(403);
            die('Access denied');
        }
        
        // Only allow saving in custom/ directory for safety
        $customPath = $basePath . '/custom';
        if (strpos(realpath(dirname($fullPath)) ?: dirname($fullPath), $customPath) !== 0) {
            // Check if it's a new file in custom/
            if (strpos($fullPath, $customPath) !== 0) {
                http_response_code(403);
                die('Can only save files in custom/ directory');
            }
        }
        
        // Create directory if it doesn't exist
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                http_response_code(500);
                die('Failed to create directory');
            }
        }
        
        if (file_put_contents($fullPath, $content) === false) {
            http_response_code(500);
            die('Failed to save file');
        }
        
        echo 'Saved successfully';
        break;
        
    default:
        http_response_code(400);
        die('Invalid action');
}

