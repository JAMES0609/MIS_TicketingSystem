<form action="path_to_your_form_processing_script.php" method="post">
        <label for="datetime">Date & Time:</label>
        <input type="datetime-local" id="datetime" name="datetime" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="contact">Contact Number:</label>
        <input type="tel" id="contact" name="contact" required>

        <label for="supervisor">Head/Supervisor:</label>
        <input type="text" id="supervisor" name="supervisor" required>

        <label for="institute">Institute/Office:</label>
        <input type="text" id="institute" name="institute" required>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="">Please select</option>
            <option value="electrical">Electrical</option>
            <option value="plumbing">Plumbing</option>
            <option value="mechanical">Mechanical</option>
            <option value="general">General Maintenance</option>
            <!-- Add more categories as needed -->
        </select>

        <label for="location">Location of Work:</label>
        <input type="text" id="location" name="location" required>

        <label for="description">Description of Work Request:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <div class="radio-group">
            <span class="radio-label">Priority:</span>
            <label for="low" class="radio-label"><input type="radio" id="low" name="priority" value="low" required>Low</label>
            <label for="medium" class="radio-label"><input type="radio" id="medium" name="priority" value="medium" required>Medium</label>
            <label for="high" class="radio-label"><input type="radio" id="high" name="priority" value="high" required>High</label>
        </div>

        <button type="submit">Submit Request</button>