<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function setToast(string $message, string $type = 'success') {
    $_SESSION['toast'] = ['msg' => $message, 'type' => $type];
}

function renderToast() {
    if (empty($_SESSION['toast'])) return;
    $t = $_SESSION['toast'];
    unset($_SESSION['toast']);
    $type = $t['type'] === 'error' ? 'error' : 'success';
    $icon = $type === 'error' ? '✕' : '✓';
    $msg = htmlspecialchars($t['msg'], ENT_QUOTES);
    echo <<<HTML
    <div id="admin-toast" class="admin-toast {$type}">
        <span class="admin-toast-icon">{$icon}</span>
        <span>{$msg}</span>
    </div>
    <style>
    .admin-toast{position:fixed;top:20px;right:20px;z-index:9999;display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;color:#fff;font:600 13px/1.4 system-ui,sans-serif;box-shadow:0 10px 30px rgba(0,0,0,.25);max-width:320px;animation:toastIn .3s ease}
    .admin-toast.success{background:#16a34a}
    .admin-toast.error{background:#dc2626}
    .admin-toast-icon{width:24px;height:24px;flex-shrink:0;background:rgba(255,255,255,.25);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px}
    .admin-toast.hide{opacity:0;transform:translateX(20px);transition:.3s}
    @keyframes toastIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:none}}
    </style>
    <script>
    (function(){var el=document.getElementById('admin-toast');if(!el)return;setTimeout(function(){el.classList.add('hide');setTimeout(function(){el.remove()},350)},3000);el.onclick=function(){el.remove()}})();
    </script>
    HTML;
}
