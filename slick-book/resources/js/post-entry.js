
window.wrapWithCodeTag = function(code, isLn = false) {
    const textarea = document.getElementById("post-markdown");

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    const before = textarea.value.substring(0, start);
    const after = textarea.value.substring(end);

    if (['toc', 'br'].indexOf(code) >= 0) {
        textarea.value = `${before}[${code}]${after}`;
        textarea.setSelectionRange(start + code.length, start + code.length + selectedText.length);
        return;
    }

    let startMdCode = window.getStartMdCode(code);

    let newText = '';
    if (isLn) {
        newText = `${before}[${startMdCode}]\n${selectedText}\n[/${code}]${after}`;
    } else {
        newText = `${before}[${startMdCode}]${selectedText}[/${code}]${after}`;
    }
    textarea.value = newText;

    // カーソル位置を調整（選択を再設定してもよい）
    // textarea.focus();
    textarea.setSelectionRange(start + startMdCode.length, start + startMdCode.length + selectedText.length);
};

window.getStartMdCode = function(code) {
    switch (code) {
        case 'code':
            return 'code lang="shell"';
        default:
            return code;
    }
};

window.appendImgTag = function(id) {
    const textarea = document.getElementById("post-markdown");

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    const before = textarea.value.substring(0, start);
    const after = textarea.value.substring(end);

    let startMdCode = `img id="${id}"`;
    let newText = `${before}[${startMdCode} /]${after}`;

    textarea.value = newText;
    textarea.setSelectionRange(start + startMdCode.length, start + startMdCode.length + selectedText.length);
};
