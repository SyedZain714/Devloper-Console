<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $current_user;
if (!is_admin($current_user)) {
    die('Unauthorized - Admin access required');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SuiteCRM Developer Console</title>
    <meta charset="UTF-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            display: flex; 
            height: 100vh; 
            background: #1e1e1e;
            color: #d4d4d4;
        }
        
        #sidebar {
            width: 300px;
            background: #252526;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #3c3c3c;
        }
        
        #sidebarHeader {
            padding: 12px 16px;
            background: #333333;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #cccccc;
            border-bottom: 1px solid #3c3c3c;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        #sidebarHeader button {
            background: transparent;
            border: none;
            color: #cccccc;
            cursor: pointer;
            padding: 4px;
            font-size: 14px;
        }
        
        #sidebarHeader button:hover {
            color: white;
        }
        
        #searchBox {
            padding: 8px 12px;
            border-bottom: 1px solid #3c3c3c;
        }
        
        #searchBox input {
            width: 100%;
            background: #3c3c3c;
            border: 1px solid #3c3c3c;
            color: #d4d4d4;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        #searchBox input:focus {
            outline: none;
            border-color: #007acc;
        }
        
        #fileTree {
            flex: 1;
            overflow-y: auto;
            overflow-x: auto;
            padding: 4px 0;
            font-size: 13px;
        }
        
        #fileTree::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        
        #fileTree::-webkit-scrollbar-track {
            background: #1e1e1e;
        }
        
        #fileTree::-webkit-scrollbar-thumb {
            background: #424242;
            border-radius: 5px;
        }
        
        #fileTree ul {
            list-style: none;
            padding-left: 0;
        }
        
        #fileTree ul ul {
            padding-left: 16px;
            display: none;
        }
        
        #fileTree ul ul.open {
            display: block;
        }
        
        #fileTree li {
            white-space: nowrap;
        }
        
        #fileTree .tree-item {
            display: flex;
            align-items: center;
            padding: 3px 8px 3px 4px;
            cursor: pointer;
            user-select: none;
        }
        
        #fileTree .tree-item:hover {
            background: #2a2d2e;
        }
        
        #fileTree .tree-item.selected {
            background: #094771;
        }
        
        #fileTree .tree-icon {
            width: 16px;
            height: 16px;
            margin-right: 4px;
            font-size: 14px;
            text-align: center;
            flex-shrink: 0;
        }
        
        #fileTree .folder-icon { color: #dcdc9d; }
        #fileTree .file-icon { color: #d4d4d4; }
        #fileTree .php-icon { color: #777bb3; }
        #fileTree .js-icon { color: #f7df1e; }
        #fileTree .css-icon { color: #264de4; }
        #fileTree .html-icon { color: #e34c26; }
        #fileTree .json-icon { color: #cbcb41; }
        #fileTree .tpl-icon { color: #ff9800; }
        
        #fileTree .tree-label {
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        #fileTree .arrow {
            width: 16px;
            font-size: 10px;
            color: #858585;
            flex-shrink: 0;
        }
        
        #main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        
        #topBar {
            height: 48px;
            background: #333333;
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            border-bottom: 1px solid #3c3c3c;
        }
        
        #topBar button {
            background: #0e639c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        #topBar button:hover {
            background: #1177bb;
        }
        
        #topBar button:disabled {
            background: #555;
            cursor: not-allowed;
        }
        
        #topBar button.secondary {
            background: #3c3c3c;
        }
        
        #topBar button.secondary:hover {
            background: #4c4c4c;
        }
        
        #currentFile {
            flex: 1;
            color: #cccccc;
            font-size: 13px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: 'Consolas', 'Monaco', monospace;
        }
        
        #currentFile:empty::after {
            content: 'No file selected';
            color: #808080;
            font-family: 'Segoe UI', sans-serif;
        }
        
        #currentFile.modified::after {
            content: ' •';
            color: #e2c08d;
        }
        
        #status {
            font-size: 12px;
            color: #4ec9b0;
            padding: 4px 8px;
            border-radius: 3px;
        }
        
        #status.error {
            color: #f48771;
        }
        
        #editorContainer {
            flex: 1;
            position: relative;
        }
        
        #editor {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        #welcomeScreen {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6d6d6d;
            background: #1e1e1e;
        }
        
        #welcomeScreen h1 {
            font-size: 24px;
            font-weight: 300;
            margin-bottom: 16px;
            color: #858585;
        }
        
        #welcomeScreen p {
            font-size: 14px;
            margin: 4px 0;
        }
        
        #welcomeScreen kbd {
            background: #3c3c3c;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        
        #statusBar {
            height: 24px;
            background: #007acc;
            display: flex;
            align-items: center;
            padding: 0 12px;
            font-size: 12px;
            color: white;
            gap: 16px;
        }
        
        #statusBar .spacer {
            flex: 1;
        }
        
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #808080;
        }
        
        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>

<div id="sidebar">
    <div id="sidebarHeader">
        <span>Explorer</span>
        <button onclick="refreshFileTree()" title="Refresh">🔄</button>
    </div>
    <div id="searchBox">
        <input type="text" id="searchInput" placeholder="Search files..." onkeyup="filterTree(this.value)">
    </div>
    <div id="fileTree"><div class="loading">Loading...</div></div>
</div>

<div id="main">
    <div id="topBar">
        <button onclick="saveFile()" id="saveBtn" disabled>💾 Save</button>
        <button onclick="reloadFile()" id="reloadBtn" class="secondary" disabled>↻ Reload</button>
        <span id="currentFile"></span>
        <span id="status"></span>
    </div>
    <div id="editorContainer">
        <div id="welcomeScreen">
            <h1>SuiteCRM Developer Console</h1>
            <p>Select a file from the explorer to start editing</p>
            <p style="margin-top: 16px;"><kbd>Ctrl</kbd> + <kbd>S</kbd> to save</p>
        </div>
        <div id="editor" class="hidden"></div>
    </div>
    <div id="statusBar">
        <span id="cursorPos">Ln 1, Col 1</span>
        <span class="spacer"></span>
        <span id="fileMode">-</span>
        <span id="fileEncoding">UTF-8</span>
    </div>
</div>

<script>
const editor = ace.edit("editor");
editor.setTheme("ace/theme/monokai");
editor.session.setMode("ace/mode/php");
editor.setOptions({
    fontSize: "14px",
    showPrintMargin: false,
    enableBasicAutocompletion: true,
    enableLiveAutocompletion: true,
    tabSize: 4,
    useSoftTabs: true
});

let currentFilePath = "";
let originalContent = "";
let fileTreeData = null;

// Update cursor position in status bar
editor.selection.on('changeCursor', function() {
    const pos = editor.getCursorPosition();
    document.getElementById('cursorPos').textContent = `Ln ${pos.row + 1}, Col ${pos.column + 1}`;
});

// Track changes
editor.on('change', function() {
    updateModifiedState();
});

function updateModifiedState() {
    const hasChanges = editor.getValue() !== originalContent;
    document.getElementById('saveBtn').disabled = !hasChanges || !currentFilePath;
    
    const fileEl = document.getElementById('currentFile');
    if (hasChanges && currentFilePath) {
        fileEl.classList.add('modified');
    } else {
        fileEl.classList.remove('modified');
    }
}

function setStatus(msg, isError = false) {
    const el = document.getElementById('status');
    el.textContent = msg;
    el.className = isError ? 'error' : '';
    if (msg) {
        setTimeout(() => { el.textContent = ''; }, 3000);
    }
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        'php': ['🐘', 'php-icon'],
        'js': ['📜', 'js-icon'],
        'css': ['🎨', 'css-icon'],
        'html': ['🌐', 'html-icon'],
        'htm': ['🌐', 'html-icon'],
        'tpl': ['📄', 'tpl-icon'],
        'json': ['{ }', 'json-icon'],
        'xml': ['📋', 'file-icon'],
        'sql': ['🗃️', 'file-icon'],
        'md': ['📝', 'file-icon'],
        'txt': ['📄', 'file-icon'],
        'htaccess': ['⚙️', 'file-icon'],
        'log': ['📋', 'file-icon']
    };
    return icons[ext] || ['📄', 'file-icon'];
}

function getMode(path) {
    const ext = path.split('.').pop().toLowerCase();
    const modes = {
        'php': 'php',
        'js': 'javascript',
        'css': 'css',
        'html': 'html',
        'htm': 'html',
        'tpl': 'smarty',
        'json': 'json',
        'xml': 'xml',
        'sql': 'sql',
        'md': 'markdown',
        'txt': 'text',
        'log': 'text'
    };
    return modes[ext] || 'text';
}

function buildTreeHTML(data, path = '') {
    let html = '<ul' + (path === '' ? '' : '') + '>';
    
    // Sort: folders first, then files, both alphabetically
    const folders = [];
    const files = [];
    
    for (const name in data) {
        if (data[name] === null) {
            files.push(name);
        } else {
            folders.push(name);
        }
    }
    
    folders.sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
    files.sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
    
    // Render folders
    for (const name of folders) {
        const fullPath = path ? path + '/' + name : name;
        const isOpen = fullPath === 'custom' || fullPath === 'modules'; // Auto-expand these
        html += '<li>';
        html += `<div class="tree-item" data-type="folder" data-path="${fullPath}" onclick="toggleFolder(this)">`;
        html += `<span class="arrow">${isOpen ? '▼' : '▶'}</span>`;
        html += '<span class="tree-icon folder-icon">📁</span>';
        html += `<span class="tree-label">${name}</span>`;
        html += '</div>';
        html += buildTreeHTML(data[name], fullPath).replace('<ul', '<ul class="' + (isOpen ? 'open' : '') + '"');
        html += '</li>';
    }
    
    // Render files
    for (const name of files) {
        const fullPath = path ? path + '/' + name : name;
        const [icon, iconClass] = getFileIcon(name);
        html += '<li>';
        html += `<div class="tree-item" data-type="file" data-path="${fullPath}" onclick="openFile('${fullPath.replace(/'/g, "\\'")}')">`;
        html += '<span class="arrow"></span>';
        html += `<span class="tree-icon ${iconClass}">${icon}</span>`;
        html += `<span class="tree-label">${name}</span>`;
        html += '</div>';
        html += '</li>';
    }
    
    html += '</ul>';
    return html;
}

function toggleFolder(el) {
    const ul = el.nextElementSibling;
    const arrow = el.querySelector('.arrow');
    if (ul && ul.tagName === 'UL') {
        ul.classList.toggle('open');
        arrow.textContent = ul.classList.contains('open') ? '▼' : '▶';
    }
}

async function loadFileTree() {
    try {
        const res = await fetch("index.php?entryPoint=DevConsoleAPI&action=list");
        if (!res.ok) throw new Error('Failed to load');
        
        fileTreeData = await res.json();
        document.getElementById("fileTree").innerHTML = buildTreeHTML(fileTreeData);
    } catch (err) {
        document.getElementById("fileTree").innerHTML = '<div class="loading" style="color:#f48771;">Error loading files</div>';
        console.error(err);
    }
}

function refreshFileTree() {
    document.getElementById("fileTree").innerHTML = '<div class="loading">Loading...</div>';
    loadFileTree();
}

function filterTree(query) {
    query = query.toLowerCase().trim();
    const items = document.querySelectorAll('#fileTree .tree-item');
    
    if (!query) {
        // Show all, collapse to default state
        items.forEach(item => {
            item.parentElement.style.display = '';
        });
        document.querySelectorAll('#fileTree ul ul').forEach(ul => {
            ul.classList.remove('open');
            if (ul.parentElement.querySelector('.tree-item')?.dataset.path === 'custom' ||
                ul.parentElement.querySelector('.tree-item')?.dataset.path === 'modules') {
                ul.classList.add('open');
            }
        });
        return;
    }
    
    // Expand all and filter
    document.querySelectorAll('#fileTree ul ul').forEach(ul => ul.classList.add('open'));
    
    items.forEach(item => {
        const path = item.dataset.path.toLowerCase();
        const name = item.querySelector('.tree-label').textContent.toLowerCase();
        const matches = name.includes(query) || path.includes(query);
        
        if (item.dataset.type === 'file') {
            item.parentElement.style.display = matches ? '' : 'none';
        }
    });
}

async function openFile(path) {
    // Check for unsaved changes
    if (currentFilePath && editor.getValue() !== originalContent) {
        if (!confirm('You have unsaved changes. Discard them?')) {
            return;
        }
    }
    
    try {
        setStatus('Loading...');
        const res = await fetch("index.php?entryPoint=DevConsoleAPI&action=open&path=" + encodeURIComponent(path));
        
        if (!res.ok) {
            const error = await res.text();
            throw new Error(error);
        }
        
        const text = await res.text();
        
        // Show editor, hide welcome
        document.getElementById('welcomeScreen').classList.add('hidden');
        document.getElementById('editor').classList.remove('hidden');
        editor.resize();
        
        editor.setValue(text, -1);
        originalContent = text;
        currentFilePath = path;
        
        document.getElementById("currentFile").textContent = path;
        document.getElementById("currentFile").classList.remove('modified');
        document.getElementById("saveBtn").disabled = true;
        document.getElementById("reloadBtn").disabled = false;
        
        // Set editor mode based on file extension
        const mode = getMode(path);
        editor.session.setMode("ace/mode/" + mode);
        document.getElementById("fileMode").textContent = mode.toUpperCase();
        
        // Update selection in tree
        document.querySelectorAll('#fileTree .tree-item.selected').forEach(el => el.classList.remove('selected'));
        document.querySelector(`#fileTree .tree-item[data-path="${path}"]`)?.classList.add('selected');
        
        setStatus('File loaded');
    } catch (err) {
        setStatus('Error: ' + err.message, true);
        console.error(err);
    }
}

async function reloadFile() {
    if (currentFilePath) {
        if (editor.getValue() !== originalContent) {
            if (!confirm('Discard unsaved changes and reload?')) {
                return;
            }
        }
        await openFile(currentFilePath);
    }
}

async function saveFile() {
    if (!currentFilePath) {
        setStatus('No file selected', true);
        return;
    }

    try {
        setStatus('Saving...');
        
        const body = new FormData();
        body.append("path", currentFilePath);
        body.append("content", editor.getValue());

        const res = await fetch("index.php?entryPoint=DevConsoleAPI&action=save", { 
            method: "POST", 
            body 
        });
        
        const result = await res.text();
        
        if (res.ok) {
            originalContent = editor.getValue();
            document.getElementById("currentFile").classList.remove('modified');
            document.getElementById("saveBtn").disabled = true;
            setStatus('✓ Saved');
        } else {
            setStatus('Error: ' + result, true);
        }
    } catch (err) {
        setStatus('Error saving file', true);
        console.error(err);
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        if (!document.getElementById('saveBtn').disabled) {
            saveFile();
        }
    }
});

// Warn before leaving with unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (currentFilePath && editor.getValue() !== originalContent) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Load file tree on start
loadFileTree();
</script>

</body>
</html>

