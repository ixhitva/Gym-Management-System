<?php
// ...existing code...
?>
<div class="form-container">
    <h2 class="form-title">Add New Class</h2>
    <form action="process_add_class.php" method="POST">
        <div class="form-group">
            <label for="name">Class Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="trainer">Trainer:</label>
            <select name="trainer" id="trainer" class="form-control" required>
                <?php
                // ...existing trainer options code...
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="schedule">Schedule:</label>
            <input type="datetime-local" name="schedule" id="schedule" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Class</button>
    </form>
</div>
