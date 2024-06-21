/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});



/* ------ this is for view button tickets ------ */
document.addEventListener('DOMContentLoaded', function() {
    // Event delegation for dynamically generated "View" buttons
    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('view-btn')) {
            event.preventDefault();
            var button = event.target;
            var requestId = button.getAttribute('data-request-id');
            var description = button.getAttribute('data-description');
            var priority = button.getAttribute('data-priority');
            var ticketStatus = button.getAttribute('data-ticket-status');
            var category = button.getAttribute('data-category');
            var supervisor = button.getAttribute('data-supervisor');
            var department = button.getAttribute('data-department');
            var location = button.getAttribute('data-location');
            var name = button.getAttribute('data-name');
            var contact = button.getAttribute('data-contact');
            var email = button.getAttribute('data-email');
            var schedule = button.getAttribute('data-schedule');
            var date = button.getAttribute('data-date');

            // Set modal content
            document.getElementById('modal-ticket-id').textContent = requestId;
            document.getElementById('modal-description').textContent = description;
            document.getElementById('modal-priority').textContent = priority;
            document.getElementById('modal-status').textContent = ticketStatus;
            document.getElementById('modal-category').textContent = category;
            document.getElementById('modal-supervisor').textContent = supervisor;
            document.getElementById('modal-department').textContent = department;
            document.getElementById('modal-location').textContent = location;
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-contact').textContent = contact;
            document.getElementById('modal-email').textContent = email;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-schedule').textContent = schedule;
            document.getElementById('modal-ticket-id-input').value = requestId;

            // Display modal
            var modal = document.getElementById('ticketModal');
            modal.style.display = 'block';
            modal.querySelector('.modal-content').style.width = '100%';
        }
    });

    // Event listener for closing the modal
    var closeButton = document.querySelector('.close');
    closeButton.addEventListener('click', function() {
        var modal = document.getElementById('ticketModal');
        modal.style.display = 'none';
    });

    // Event listener for clicking outside the modal
    window.onclick = function(event) {
        var modal = document.getElementById('ticketModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };

    var searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        var searchTerm = this.value.trim();

        // Define the URL for fetching data
        var url = searchTerm === '' ? 'path_to_your_php_script.php?page=1' : 'search_tickets.php';

        // Prepare the request body or query string
        var body = searchTerm === '' ? null : 'search=' + encodeURIComponent(searchTerm);
        var method = searchTerm === '' ? 'GET' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: body
        })
        .then(response => response.json())
        .then(data => updateTable(data))
        .catch(error => console.error('Error:', error));
    });

    function updateTable(data) {
        var tbody = document.querySelector('.table-responsive-tickets tbody');
        tbody.innerHTML = ''; // Clear current table body
        if (data.length) {
            data.forEach(row => {
                var tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="td-items"><input type="checkbox" name="selected_tickets[]" value="${row.id}"></td>
                    <td class="td-items">${row.request_id}</td>
                    <td class="td-items">${row.priority}</td>
                    <td class="td-items">${row.category}</td>
                    <td class="td-items">${row.department}</td>
                    <td class="td-items">${row.ticket_status}</td>
                    <td class="td-items">${row.date}</td>
                    <td class="td-items">
                        <button type="button" class="btn btn-primary btn-sm view-btn"
                                data-request-id="${row.request_id}"
                                data-description="${row.description}"
                                data-priority="${row.priority}"
                                data-ticket-status="${row.ticket_status}"
                                data-category="${row.category}"
                                data-supervisor="${row.supervisor}"
                                data-department="${row.department}"
                                data-location="${row.location}"
                                data-name="${row.name}"
                                data-contact="${row.contact}"
                                data-email="${row.email}"
                                data-schedule="${row.schedule}"
                                data-date="${row.date}">
                            View
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="8">No tickets found</td></tr>';
        }
    }
});


// ------ for filters ------ //
document.addEventListener('DOMContentLoaded', function() {
    var filters = document.querySelectorAll('.filter-dropdown');
    filters.forEach(function(filter) {
        filter.addEventListener('change', function() {
            applyFilters();
        });
    });

    function applyFilters() {
        var priorityFilter = document.getElementById('filter-priority').value;
        var categoryFilter = document.getElementById('filter-category').value;
        var departmentFilter = document.getElementById('filter-department').value;
        var statusFilter = document.getElementById('filter-status').value;

        var rows = document.querySelectorAll('.table-responsive-tickets tbody tr');
        rows.forEach(function(row) {
            var priority = row.cells[2].textContent;
            var category = row.cells[3].textContent;
            var department = row.cells[4].textContent;
            var status = row.cells[5].textContent;

            var priorityMatch = !priorityFilter || priority === priorityFilter;
            var categoryMatch = !categoryFilter || category === categoryFilter;
            var departmentMatch = !departmentFilter || department.includes(departmentilter);
            var statusMatch = !statusFilter || status === statusFilter;

            if (priorityMatch && categoryMatch && departmentMatch && statusMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var filters = document.querySelectorAll('.filter-dropdown, .date-filter input');
    filters.forEach(function(filter) {
        filter.addEventListener('change', function() {
            applyFilters();
        });
    });

    function applyFilters() {
        var priorityFilter = document.getElementById('filter-priority').value;
        var categoryFilter = document.getElementById('filter-category').value;
        var departmentFilter = document.getElementById('filter-department').value;
        var statusFilter = document.getElementById('filter-status').value;
        var startDateFilter = document.getElementById('filter-date-start').value;
        var endDateFilter = document.getElementById('filter-date-end').value;

        var rows = document.querySelectorAll('.table-responsive-tickets tbody tr');
        rows.forEach(function(row) {
            var priority = row.cells[2].textContent;
            var category = row.cells[3].textContent;
            var department = row.cells[4].textContent;
            var status = row.cells[5].textContent;
            var dateCreated = row.cells[6].textContent; // Assuming this is the index for Date Created

            var priorityMatch = !priorityFilter || priority === priorityFilter;
            var categoryMatch = !categoryFilter || category === categoryFilter;
            var departmentMatch = !departmentFilter || department.includes(departmentFilter);
            var statusMatch = !statusFilter || status === statusFilter;
            var dateMatch = (!startDateFilter || new Date(dateCreated) >= new Date(startDateFilter)) &&
                            (!endDateFilter || new Date(dateCreated) <= new Date(endDateFilter));

            if (priorityMatch && categoryMatch && departmentMatch && statusMatch && dateMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
});



// for page
function handlePerPageChange(value) {
    var newLocation = '?perPage=' + value;
    if (value === 'all') {
        newLocation += '&page=1'; // Ensure that pagination resets when showing all
    }
    location = newLocation;}

