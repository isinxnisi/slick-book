window.addTagToLeftUI = function (groupId, tagId, tagName, background = '#4f46e5', textColor = '#fff') {
    const $groupUl = $(`#tags-of-group-${groupId}`);

    if ($groupUl.length === 0) {
        console.warn('対象グループが見つかりません:', groupId);
        return;
    }

    if ($groupUl.find(`[data-tag-id="${tagId}"]`).length === 0) {
        const li = `
            <li class="flex items-center space-x-1 tag-item active ps-2 pe-2 py-1 rounded-full text-sm ui-sortable-handle"
                data-tag-id="${tagId}"
                style="background-color: ${background}; color: ${textColor}; border: 1px solid ${background}">
                <span>${tagName}</span>
                <button class="delete-tag-btn hover:text-red-400" data-tag-id="${tagId}">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </li>
        `;
        $groupUl.append(li);
        lucide.createIcons();
    }
};


window.removeTagFromLeftUI = function (groupId, tagId) {
    $(`#tags-of-group-${groupId} [data-tag-id="${tagId}"]`).remove();
};

window.removeTagFromRightUI = function (tagId) {
    $(`.tag-toggle-btn[data-tag-id="${tagId}"]`).each(function () {
        const $btn = $(this);
        $btn.removeClass('active');
        const borderColor = $btn.css('border-color');
        $btn.css({ backgroundColor: 'transparent', color: borderColor });
    });
};

window.refreshLeftTagUI = function () {
    const groupId = window.currentSelectedGroupId;
    if (!groupId) return;
    let style = { bg: '#888', text: '#fff' };

    $.get(`/tag-groups/${groupId}/tags`, function (tags) {
        const $ul = $(`#tags-of-group-${groupId}`);
        $ul.empty();

        tags.forEach(tag => {
            style = window.purposeStyles?.[tag.purpose] || { bg: '#888', text: '#fff' };
            const li = `
                <li class="flex items-center space-x-1 tag-item active ps-2 pe-2 py-1 rounded-full text-sm ui-sortable-handle"
                    data-tag-id="${tag.id}"
                    style="background-color: ${style.bg}; color: ${style.text}; border: 1px solid ${style.bg}">
                    <span>${tag.name}</span>
                    <button class="delete-tag-btn hover:text-red-400" data-tag-id="${tag.id}">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </li>
            `;
            $ul.append(li);
        });

        lucide.createIcons();
    });
};

window.refreshLucideAndBindEvents = function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    // タグUI右側イベントを再バインド
    if (typeof bindRightUIEvents === 'function') {
        bindRightUIEvents();
    }
};
