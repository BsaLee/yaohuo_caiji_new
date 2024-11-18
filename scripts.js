var currentPage = 1;
var searchTerm = ''; // 存储搜索词

window.onload = function () {
    loadPosts(currentPage, searchTerm); // 初始加载第一页的帖子
};

// 加载帖子数据
function loadPosts(page, searchTerm) {
    var xhr = new XMLHttpRequest();
    var url = '/shouye.php?page=' + page; // 默认请求首页数据

    if (searchTerm) {
        url = '/search.php?page=' + page + '&search=' + encodeURIComponent(searchTerm); // 如果有搜索词，调用搜索接口
    }

    xhr.open('GET', url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                displayPosts(response.data);
                displayPagination(response.pagination);
            } else {
                console.error('Failed to load posts:', response);
            }
        }
    };
    xhr.send();
}

// 显示帖子
function displayPosts(posts) {
    var container = document.getElementById('postsContainer');
    container.innerHTML = ''; // 清空现有内容

    if (posts.length === 0) {
        container.innerHTML = '<p>没有找到相关结果。</p>'; // 没有搜索到内容时显示提示
    } else {
        posts.forEach(function (post) {
            var postDiv = document.createElement('div');
            postDiv.className = 'post';
            
            var title = document.createElement('a');
            title.className = 'title';
            title.textContent = post.title;
            title.href = '/neirong.html?id=' + post.id;
            title.target = '_self';

            var info = document.createElement('div');
            info.className = 'info';
            var status = post.isok ? '已采集' : '未采集';
            info.innerHTML = '作者: ' + post.author +
                ' | 阅读: ' + post.replies +
                ' | 回复: ' + post.views +
                ' | 状态: ' + status +
                '<br>发布: ' + post.time +
                ' | 采集: ' + post.created_at;

            postDiv.appendChild(title);
            postDiv.appendChild(info);
            container.appendChild(postDiv);
        });
    }
}

// 显示分页信息
function displayPagination(pagination) {
    var paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = ''; // 清空现有分页内容

    var prevButton = document.createElement('button');
    prevButton.textContent = '上一页';
    prevButton.onclick = function () {
        if (currentPage > 1) {
            currentPage--;
            loadPosts(currentPage, searchTerm); // 传递当前搜索词
        }
    };
    prevButton.disabled = currentPage === 1;
    paginationContainer.appendChild(prevButton);

    var pageInfo = document.createElement('span');
    pageInfo.textContent = '第 ' + pagination.current_page + ' 页，共 ' + pagination.total_pages + ' 页';
    paginationContainer.appendChild(pageInfo);

    var nextButton = document.createElement('button');
    nextButton.textContent = '下一页';
    nextButton.onclick = function () {
        if (currentPage < pagination.total_pages) {
            currentPage++;
            loadPosts(currentPage, searchTerm); // 传递当前搜索词
        }
    };
    nextButton.disabled = currentPage === pagination.total_pages;
    paginationContainer.appendChild(nextButton);
}

// 搜索功能：点击搜索按钮后，加载搜索结果
document.getElementById('searchButton').addEventListener('click', function() {
    searchTerm = document.getElementById('searchInput').value.trim(); // 获取搜索框内容
    currentPage = 1; // 搜索时从第一页开始
    loadPosts(currentPage, searchTerm); // 请求搜索接口
});

// 如果按下回车键，触发搜索
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchButton').click(); // 模拟点击搜索按钮
    }
});
