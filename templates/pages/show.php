<div class="show">
    <div class="message">
        <?php
            if (!empty($params['before'])) {
                $message = match($params['before']) {
                    'updated' => 'Note updated successfully',
                    default => 'Process completed successfully'
                };

                echo $message;
            }
        ?>
    </div>
    <?php if (!empty($params['note'])): ?>
        <ul>
            <li>Id: <?= $params['note']['id'] ?? null; ?></li>
            <li>Title: <?= $params['note']['title'] ?? null; ?></li>
            <li>Description: <?= $params['note']['description'] ?? null; ?></li>
            <li>Created: <?= $params['note']['created'] ?? null; ?></li>
        </ul>
        <a href="/?action=edit&id=<?= $params['note']['id']; ?>">
            <button>Edit</button>
        </a>
    <?php else: ?>
        <div>
            No note to display
        </div>
    <?php endif; ?>
    <a href="/">
        <button>Return to notes list</button>
    </a>
</div>