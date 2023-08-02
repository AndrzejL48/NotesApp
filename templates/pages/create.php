<div>
    <h3>Add note</h3>
    <div>
        <div class="error-message">
            <?php
                if (!empty($params['error'])) {
                    $errorMessage = match($params['error']) {
                        'emptyRequiredFields' => !empty($params['error_message']) ? $params['error_message'] : 'Required fields cannot be empty',
                        default => 'unknown error has occurred'
                    };

                    echo $errorMessage;
                }
            ?>
        </div>
        <form class="note-form" action="/?action=create"
        method="post">
            <ul>
                <li>
                    <label>Title <span class="required">*</span></label>
                    <input type="text" name="title" class="field-long"/>
                </li>
                <li>
                    <label>Contents <span class="required">*</span></label>
                    <textarea name="description" id="field5"
                    class="field-long field-textarea"></textarea>
                </li>
                <li>
                    <input type="submit" value="Zapisz"/>
                </li>
            </ul>
        </form>
    </div>
</div>