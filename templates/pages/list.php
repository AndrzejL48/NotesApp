<div class="list">
    <section>
        <div class="error-message">
            <?php
                if (!empty($params['error'])) {
                    $errorMessage = match($params['error']) {
                        'missingNoteId' => 'Invalid note identifier',
                        'noteNotFound' => 'Note not found',
                        default => 'unknown error has occurred'
                    };

                    echo $errorMessage;
                }
            ?>
        </div>
        <div class="message">
            <?php
                if (!empty($params['before'])) {
                    $message = match($params['before']) {
                        'created' => 'Note created successfully',
                        'deleted' => 'Note deleted succesfully',
                        default => 'Process completed successfully'
                    };

                    echo $message;
                }
            ?>
        </div>

        <form class="settings-form" action="/" method="GET">
            <input type="text" name="search" value="<?= $params['search']; ?>" placeholder="Wpisz frazę"/>
            <input type="submit" value="Search"/>
            <a href="/">
                <input type="button" value="Clear"/>
            </a>
        </form>

        <table cellpadding="0" cellspacing="0" border="0">
            <thead class="tbl-header">
                <tr>
                    <th>Id</th>
                    <th>Title</th>
                    <th>Created</th>
                    <th>Options</th>
                </tr>
                <tr>
                    <th style="width: auto;">
                        <div class="sort-container">
                            <a href="/?sort_by=id&sort_order=asc"
                            class="sort-arrow-up sort-arrow-rotate">
                            </a>
                            <a href="/?sort_by=id&sort_order=desc"
                            class="sort-arrow-down sort-arrow-rotate">
                            </a>
                        </div>
                    </th>
                    <th style="width: auto;">
                        <div class="sort-container">
                            <a href="/?sort_by=title&sort_order=asc"
                            class="sort-arrow-up sort-arrow-rotate">
                            </a>
                            <a href="/?sort_by=title&sort_order=desc"
                            class="sort-arrow-down sort-arrow-rotate">
                            </a>
                        </div>
                    </th>
                    <th style="width: auto;">
                        <div class="sort-container">
                            <a href="/?sort_by=created&sort_order=asc"
                            class="sort-arrow-up sort-arrow-rotate">
                            </a>
                            <a href="/?sort_by=created&sort_order=desc"
                            class="sort-arrow-down sort-arrow-rotate">
                            </a>
                        </div>
                    </th>
                    <th>
                    </th>
                </tr>
            </thead>
            <tbody class="tbl-content" id="table-body">
                <?php foreach ($params['notes'] ?? [] as $param): ?>
                    <tr>
                        <td><?= $param['id']; ?></td>
                        <td><?= $param['title']; ?></td>
                        <td><?= $param['created']; ?></td>
                        <td>
                            <a href="/?action=show&id=<?= $param['id']; ?>">
                                <button>Show</button>
                            </a>
                            <button class="delete-button" note-id="<?= $param['id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <ul class="pagination">
            <?php
                $nextPageNumber = $params['pagination']['current_page'] + 1;
                $previousPageNumber = $params['pagination']['current_page'] - 1;
            ?>
            <li>
                <a href="/?page_number=<?= $previousPageNumber; ?>">
                    <?php if ($previousPageNumber < 1): ?>
                        <button disabled class="inactive">
                            <<
                        </button>
                    <?php else: ?>
                        <button>
                            <<
                        </button>
                    <?php endif; ?>
                </a>
            </li>
            <?php for ($pageNumber = 1; $pageNumber <= $params['pagination']['pages_amount']; $pageNumber++): ?>
                <li class="<?= $pageNumber == $params['pagination']['current_page'] ? 'active' : '' ?>">
                    <a href="/?page_number=<?= $pageNumber; ?>">
                        <button><?= $pageNumber; ?></button>
                    </a>
                </li>
            <?php endfor; ?>
            <li>
                <a href="/?page_number=<?= $nextPageNumber; ?>">
                    <?php if ($nextPageNumber > $params['pagination']['pages_amount']): ?>
                        <button disabled class="inactive">
                            >>
                        </button>
                    <?php else: ?>
                        <button>
                            >>
                        </button>
                    <?php endif;?>
                </a>
            </li>
        </ul>
    </section>
</div>

<script>
    let tableBody = document.getElementById("table-body");
    tableBody.addEventListener('click', function (event) {
        event.currentTarget.querySelectorAll('.delete-button').forEach(function (item) {
            let noteId = item.getAttribute('note-id');

            if (event.target.getAttribute('note-id') == noteId) {
                const deleteConfirmation = confirm('are you sure you want to delete the selected note?');

                if (deleteConfirmation === true) {
                    location.href = '/?action=delete&id=' + noteId;
                }
            }
        });
    })
</script>