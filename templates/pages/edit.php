<div>
    <h3>Edit note</h3>
    <div>
    <div class="error-message">
            <?php
                if (!empty($params['error'])) {
                    $errorMessage = match($params['error']['error']) {
                        'emptyRequiredFields' => !empty($params['error']['error_message']) ? $params['error']['error_message'] : 'Required fields cannot be empty',
                        default => 'unknown error has occurred'
                    };

                    echo $errorMessage;
                }
            ?>
        </div>
        <?php if (!empty($params['id']) && !empty($params['note'])): ?>
            <form class="note-form" action="/?action=edit&id=<?= $params['id'] ?>"
            method="POST">
                <ul>
                    <li>
                        <label>Title <span class="required">*</span></label>
                        <input type="text" name="title" class="field-long" value="<?= $params['note']['title']; ?>"/>
                    </li>
                    <li>
                        <label>Contents <span class="required">*</span></label>
                        <textarea name="description" id="field5"
                        class="field-long field-textarea"><?= $params['note']['description']; ?></textarea>
                    </li>
                    <li>
                        <input type="submit" value="Zapisz"/>
                    </li>
                </ul>
            </form>
        <?php else: ?>
            <div>
                No data to display
                <a href="/">
                    <button>Return to notes list</button>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>