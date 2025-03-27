/**
 * 右からフェードインする関数
 * @param {jQuery Object} $elem - アニメーション対象の要素
 * @param {number} duration - アニメーションの時間（ミリ秒）
 */
window.fadeSlideInRight = function ($elem, duration = 100) {
    // 初期状態：表示はブロック、透明、右に100%移動（※必要に応じてピクセル値に変更）
    $elem.css({
        display: 'block',
        opacity: 0,
        transform: 'translateX(100%)',
        transition: 'none'
    });

    // 強制リフロー（transition 適用前にスタイルを反映させるため）
    $elem[0].offsetWidth;

    // CSS3 トランジションを設定し、フェードイン＋右から左へのスライド
    $elem.css({
        transition: 'opacity ' + duration + 'ms ease, transform ' + duration + 'ms ease',
        opacity: 1,
        transform: 'translateX(0)'
    });
}