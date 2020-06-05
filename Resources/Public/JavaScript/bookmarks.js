let renderBookmarkList = (data) => {
    let bookmarkList = document.getElementById('bookmarkList');
    if (typeof(bookmarkList) !== 'undefined' && bookmarkList !== null) {
        let bookmarkListItems = [];
        Object.keys(data).map((key) => {
            let bookmark = data[key];
            bookmarkListItems.push(`
<div class="ce-tx-bookmarks__list-item">
    <div class="grid-x grid-padding-x">
        <div class="cell small-4">
            <picture>
                <img src="/typo3conf/ext/template/Resources/Public/img/logo_gray_signet_only.svg" alt="${bookmark.title}">
            </picture>
        </div>
        <div class="cell small-8">
            <div class="grid-x grid-padding-x">
                <div class="cell medium-11">
                    <h3 class="vevent-headline">
                        <a href="${bookmark.uri}" class="ce-tx-bookmarks__link" data-bookmark-id="${bookmark.id}">
                            <span class="ce-tx-bookmarks__link-title">${bookmark.title}</span>
                        </a>
                    </h3>
                </div>
                <div class="cell medium-1">
                    <div class="vevent-bookmark">
                        <a href="#" class="ce-tx-bookmarks__delete-action" onclick="deleteBookmark('${bookmark.uid}'); return false;" title="${action_delete_title}">
                            <i class="icon icon-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="cell medium-11">
                    ${bookmark.type != 'None' ? '<span class="ce-tx-bookmarks__link-type">' + bookmark.type + '</span>' : ''}
                    <div class="description show-for-medium">
                        <p>${bookmark.data ? (bookmark.data.description ? bookmark.data.description : (bookmark.data.teaser ? bookmark.data.teaser : (bookmark.data.abstract ? bookmark.data.abstract : ''))).substring(0, 150) + ' ...' : ''}</p>
                    </div>
                </div>
                <div class="cell medium-1">
                    <a href="${bookmark.uri}" class="ce-tx-bookmarks__more" data-bookmark-id="${bookmark.id}">
                        <i class="icon icon-arrow-forward"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
`);
        });
        let list = document.createElement('div');
        list.innerHTML = bookmarkListItems.join('');
        if (bookmarkList.hasChildNodes()) {
            bookmarkList.innerHTML = '';
        }
        bookmarkList.append(list);
    }
}

let renderBookmarkLinks = (data) => {
    document.querySelectorAll('a[data-bookmark]:not(.initialized)').forEach((bookmarkSelector) => {
        bookmarkSelector.classList.add("initialized");
        bookmarkSelector.classList.remove("bookmarked");
        bookmarkSelector.addEventListener('click', (e) => {
            let id = e.currentTarget.id,
                item = e.currentTarget,
                index = Object.keys(data).map((key) => {
                let el = data[key];
                return el.id;
            }).indexOf(bookmarkSelector.getAttribute('id'));
            // return if target doesn't have an id (shouldn't happen)
            if (!id) return;
            // item is not bookmarked
            if (index === -1) {
                createBookmark({
                    uid: item.dataset.bookmarkParentUid,
                    pid: item.dataset.bookmarkParentPid,
                    table: item.dataset.bookmarkParentTable
                });
                item.classList.add("bookmarked");
            } else {
                deleteBookmark(item.dataset.bookmarkUid);
                item.classList.remove("bookmarked");
                delete item.dataset.bookmarkUid;
            }
            e.preventDefault();
        }, false);
    });
    Object.keys(data).map((key) => {
        let bookmark = data[key];
        let bookmarkLink = document.getElementById(bookmark.id);
        if (typeof(bookmarkLink) !== 'undefined' && bookmarkLink !== null) {
            document.getElementById(bookmark.id).classList.add("bookmarked");
            document.getElementById(bookmark.id).dataset.bookmarkUid = bookmark.uid;
        }
    });
}

let getBookmarks = () => {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let data = JSON.parse(this.responseText);
            renderBookmarkList(data);
            renderBookmarkLinks(data);
            updateBookmarkBadgeCounter(Object.keys(data).length);
        }
    };
    let uri = "/?tx_bookmarks_bookmark[action]=list&tx_bookmarks_bookmark[controller]=Ajax&type=12244";
    xhr.open("GET", uri, true);
    xhr.send();
}

let createBookmark = (data) => {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            getBookmarks();
        }
    };
    let params = 'tx_bookmarks_bookmark[bookmark][parentUid]=' + data.uid + '&tx_bookmarks_bookmark[bookmark][parentPid]=' + data.pid + '&tx_bookmarks_bookmark[bookmark][parentTable]=' + data.table + '';
    let uri = "/?tx_bookmarks_bookmark[action]=create&tx_bookmarks_bookmark[controller]=Ajax&type=12244";
    xhr.open("POST", uri, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
}

let deleteBookmark = (uid) => {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            getBookmarks();
        }
    };
    let uri = "/?tx_bookmarks_bookmark[action]=delete&tx_bookmarks_bookmark[controller]=Ajax&tx_bookmarks_bookmark[bookmark]=" + uid + "&type=12244";
    xhr.open("DELETE", uri, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send();
}

let updateBookmarkBadgeCounter = (count) => {
    document.querySelectorAll('.bookmarks-badge-counter').forEach((bookmarkBadgeCounterSelector) => {
        bookmarkBadgeCounterSelector.innerText = count;
    });
}

getBookmarks();
