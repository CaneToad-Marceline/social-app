// untuk like atau dislike post
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const postId = this.dataset.postId;
        const formData = new FormData();
        formData.append('action', 'toggle_like');
        formData.append('post_id', postId);
        
        try {
            const response = await fetch('ajax.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.classList.toggle('liked', data.liked);
                const post = this.closest('.post');
                const likesSpan = post.querySelector('.post-stats span:first-child');
                likesSpan.textContent = `${data.likes_count} likes`;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});

// ngetoggle comments section
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    const isActive = commentsSection.classList.contains('active');
    
    if (!isActive) {
        commentsSection.classList.add('active');
        loadComments(postId);
    } else {
        commentsSection.classList.remove('active');
    }
}

// nge load comment untuk post
async function loadComments(postId) {
    const formData = new FormData();
    formData.append('action', 'get_comments');
    formData.append('post_id', postId);
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            const commentsList = document.querySelector(`#comments-${postId} .comments-list`);
            commentsList.innerHTML = '';
            
            data.comments.forEach(comment => {
                const commentHtml = `
                    <div class="comment">
                        <img src="uploads/profiles/${comment.profile_picture}" class="avatar">
                        <div class="comment-content">
                            <strong>${escapeHtml(comment.username)}</strong>
                            <p>${escapeHtml(comment.content)}</p>
                        </div>
                    </div>
                `;
                commentsList.innerHTML += commentHtml;
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// nambahhin comment
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const postId = this.dataset.postId;
        const input = this.querySelector('input');
        const content = input.value.trim();
        
        if (!content) return;
        
        const formData = new FormData();
        formData.append('action', 'add_comment');
        formData.append('post_id', postId);
        formData.append('content', content);
        
        try {
            const response = await fetch('ajax.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                const commentsList = this.nextElementSibling;
                const comment = data.comment;
                
                const commentHtml = `
                    <div class="comment">
                        <img src="uploads/profiles/${comment.profile_picture}" class="avatar">
                        <div class="comment-content">
                            <strong>${escapeHtml(comment.username)}</strong>
                            <p>${escapeHtml(comment.content)}</p>
                        </div>
                    </div>
                `;
                
                commentsList.innerHTML += commentHtml;
                input.value = '';
                
                // Update itungan comment
                const post = this.closest('.post');
                const commentSpan = post.querySelector('.post-stats span:last-child');
                const currentCount = parseInt(commentSpan.textContent);
                commentSpan.textContent = `${currentCount + 1} comments`;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]); 
} 