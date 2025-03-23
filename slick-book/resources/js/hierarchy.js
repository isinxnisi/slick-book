window.addTagToLeftUI = function (groupId, tagId, tagName, color) {
    const $groupUl = $(`#tags-of-group-${groupId}`);

    if ($groupUl.length === 0) {
        console.warn('対象グループが見つかりません:', groupId);
        return;
    }

    if ($groupUl.find(`[data-id="${tagId}"]`).length === 0) {
        const li = `
            <li class="flex items-center space-x-1 bg-indigo-700 text-white ps-2 pe-2 py-1 rounded-full text-sm ui-sortable-handle" data-id="${tagId}">
                <span>${tagName}</span>
                <button class="delete-tag-btn hover:text-red-400"
                    data-id="${tagId}">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </li>`;
        $groupUl.append(li);
    }
};

window.removeTagFromLeftUI = function (groupId, tagId) {
    $(`#tags-of-group-${groupId} [data-id="${tagId}"]`).remove();
};

window.removeTagFromRightUI = function (tagId) {
    $(`.tag-toggle-btn[data-tag-id="${tagId}"]`).each(function () {
        const $btn = $(this);
        $btn.removeClass('active');
        const borderColor = $btn.css('border-color');
        $btn.css({ backgroundColor: 'transparent', color: borderColor });
    });
};