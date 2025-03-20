// いいねボタンのクリックイベント
document.querySelectorAll('.like-button').forEach(function(button) {
    button.addEventListener('click', function() {
        // いいねカウントを増加
        let count = button.querySelector('.like-count');
        let currentCount = parseInt(count.textContent);
        count.textContent = currentCount + 1;
    });
});

// コメント送信ボタンのクリックイベント
document.querySelectorAll('.submit-comment').forEach(function(button) {
    button.addEventListener('click', function() {
        let commentInput = button.closest('.comments-section').querySelector('.comment-input');
        let commentText = commentInput.value.trim();

        // コメントが空でない場合にのみ送信
        if (commentText !== "") {
            let commentList = button.closest('.comments-section').querySelector('.comment-list');
            let newComment = document.createElement('div');
            newComment.classList.add('comment');
            newComment.textContent = commentText;
            commentList.appendChild(newComment);

            // コメント送信後、テキストエリアをクリア
            commentInput.value = '';
        }
    });
});
