<!-- Footer -->
<footer class="bg-white py-4 mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">
                &copy; <?= date('Y') ?> PT. Irgha Reksa Jasa - CHIBOR Admin Panel
            </div>
            <div>
                <span class="text-muted me-3">Version 1.0</span>
                <span class="text-muted" id="currentTime"></span>
            </div>
        </div>
    </div>
</footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Auto-hide alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            }
        });

        // Initialize DataTables
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 25,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                orderable: false,
                targets: 'no-sort'
            }]
        });

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // Image preview functionality
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                const previewId = this.id + '_preview';
                const preview = document.getElementById(previewId);

                if (file && preview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Confirm delete actions
        const deleteButtons = document.querySelectorAll('.btn-delete, [data-action="delete"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const itemName = this.getAttribute('data-item') || 'item ini';
                if (!confirm(`Apakah Anda yakin ingin menghapus ${itemName}?`)) {
                    e.preventDefault();
                }
            });
        });

        // Auto-save draft functionality
        const draftForms = document.querySelectorAll('.auto-save-draft');
        draftForms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    saveDraft(form.id, input.name, input.value);
                });
            });
        });

        // Bulk actions
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });
        }

        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkActions();
            });
        });

        // Update clock every second
        updateClock();
        setInterval(updateClock, 1000);
    });

    // Utility functions
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        const clockElement = document.getElementById('currentTime');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    function saveDraft(formId, fieldName, value) {
        const draftKey = `draft_${formId}_${fieldName}`;
        localStorage.setItem(draftKey, value);
    }

    function loadDraft(formId, fieldName) {
        const draftKey = `draft_${formId}_${fieldName}`;
        return localStorage.getItem(draftKey);
    }

    function clearDraft(formId) {
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith(`draft_${formId}_`)) {
                localStorage.removeItem(key);
            }
        });
    }

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const bulkActionsDiv = document.getElementById('bulkActions');

        if (bulkActionsDiv) {
            if (checkedBoxes.length > 0) {
                bulkActionsDiv.style.display = 'block';
                bulkActionsDiv.querySelector('.selected-count').textContent = checkedBoxes.length;
            } else {
                bulkActionsDiv.style.display = 'none';
            }
        }
    }

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '100px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    function confirmAction(message, callback) {
        if (confirm(message)) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        }
        return false;
    }

    // AJAX helper function
    function sendAjaxRequest(url, data, method = 'POST') {
        return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: method !== 'GET' ? JSON.stringify(data) : null
            })
            .then(response => response.json())
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan pada server', 'error');
            });
    }

    // Export functionality
    function exportData(format, table = 'current') {
        const url = `export.php?format=${format}&table=${table}`;
        window.open(url, '_blank');
    }

    // Print functionality
    function printTable(tableId) {
        const printWindow = window.open('', '_blank');
        const table = document.getElementById(tableId);

        if (table) {
            printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print Report</title>
                            <style>
                                body { font-family: Arial, sans-serif; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                th { background-color: #f2f2f2; }
                            </style>
                        </head>
                        <body>
                            <h2>CHIBOR Admin Report</h2>
                            ${table.outerHTML}
                        </body>
                    </html>
                `);
            printWindow.document.close();
            printWindow.print();
        }
    }

    // Search functionality
    function initializeSearch(inputId, targetClass) {
        const searchInput = document.getElementById(inputId);
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const items = document.querySelectorAll(`.${targetClass}`);

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }
</script>

<?php if (isset($extra_js)) echo $extra_js; ?>
</body>

</html>