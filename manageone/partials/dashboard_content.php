<?php
// File: partials/dashboard_content.php
// =================================================================
// This file contains the dashboard tables AND the inline forms.
// =================================================================
?>

<!-- Inline Forms Section -->
<div id="addEvaluationForm" style="display:none;" class="mb-8 bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold mb-4">Add New Website Evaluation</h3>
    <form action="add_entry.php" method="POST">
        <input type="hidden" name="type" value="evaluation">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="eval_name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="eval_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="eval_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="eval_email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="sm:col-span-2">
                <label for="website_url" class="block text-sm font-medium text-gray-700">Website URL</label>
                <input type="url" name="website_url" id="website_url" required placeholder="https://example.com" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="sm:col-span-2">
                <div class="flex items-center">
                    <input id="subscribed_newsletter" name="subscribed_newsletter" type="checkbox" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="subscribed_newsletter" class="ml-2 block text-sm text-gray-900">Subscribed to Newsletter</label>
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                Add Evaluation
            </button>
            <button type="button" onclick="toggleForm('addEvaluationForm')" class="text-gray-600 hover:text-gray-900">Cancel</button>
        </div>
    </form>
</div>

<div id="addContactForm" style="display:none;" class="mb-8 bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold mb-4">Add New Contact Submission</h3>
    <form action="add_entry.php" method="POST">
        <input type="hidden" name="type" value="contact">
        <div class="grid grid-cols-1 gap-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="contact_name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="contact_name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="contact_email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" id="subject" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="message" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
        </div>
        <div class="mt-6 flex items-center gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                Add Contact Submission
            </button>
            <button type="button" onclick="toggleForm('addContactForm')" class="text-gray-600 hover:text-gray-900">Cancel</button>
        </div>
    </form>
</div>


<!-- Search and Actions Bar -->
<div class="bg-white p-4 rounded-lg shadow-md mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <form action="admin.php" method="GET" class="flex-grow w-full sm:w-auto">
        <input type="hidden" name="page" value="dashboard">
        <div class="relative">
            <input type="text" name="search" placeholder="Search submissions..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
    </form>
     <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
        <a href="admin.php?page=dashboard" class="text-blue-600 hover:underline text-sm">Clear Search</a>
    <?php endif; ?>
</div>

<!-- Evaluations Table -->
<div class="mb-12">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-900">Website Evaluations <span class="text-lg font-normal text-gray-500">(<?php echo $evaluations_count; ?>)</span></h2>
        <div class="flex items-center gap-2">
            <button onclick="toggleForm('addEvaluationForm')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">Add New</button>
            <a href="export.php?type=evaluations" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">Export CSV</a>
        </div>
    </div>
    
    <!-- Bulk Actions for Evaluations -->
    <form id="bulkEvaluationsForm" action="admin.php?page=dashboard" method="POST">
        <input type="hidden" name="action" value="bulk_delete">
        <input type="hidden" name="item_type" value="evaluations">
        
        <div class="mb-4 bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAllEvaluations" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Select All</span>
                    </label>
                    <span id="selectedEvaluationsCount" class="text-sm text-gray-600">0 selected</span>
                </div>
                <button type="submit" id="bulkDeleteEvaluationsBtn" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" 
                        disabled 
                        onclick="return confirm('Are you sure you want to delete the selected evaluations? This action cannot be undone.')">
                    Delete Selected
                </button>
            </div>
        </div>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                     <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" disabled>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name & Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website URL</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Newsletter</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($evaluations_result->num_rows > 0): while($row = $evaluations_result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="item_ids[]" value="<?php echo $row['id']; ?>" 
                                           class="evaluation-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['name']); ?><br>
                                    <span class="text-gray-500 text-xs"><?php echo htmlspecialchars($row['email']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><a href="<?php echo htmlspecialchars($row['website_url']); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($row['website_url']); ?></a></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $row['subscribed_newsletter'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $row['subscribed_newsletter'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="admin.php?page=messages&to=<?php echo urlencode($row['email']); ?>" class="text-blue-600 hover:text-blue-900 mr-3">Message</a>
                                    <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>"><input type="hidden" name="type" value="evaluation">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No evaluation submissions found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<!-- Contacts Table -->
<div>
     <div class="flex justify-between items-center mb-4">
         <h2 class="text-2xl font-semibold text-gray-900">Contact Submissions <span class="text-lg font-normal text-gray-500">(<?php echo $contacts_count; ?>)</span></h2>
         <div class="flex items-center gap-2">
            <button onclick="toggleForm('addContactForm')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">Add New</button>
            <a href="export.php?type=contacts" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">Export CSV</a>
        </div>
    </div>
    
    <!-- Bulk Actions for Contacts -->
    <form id="bulkContactsForm" action="admin.php?page=dashboard" method="POST">
        <input type="hidden" name="action" value="bulk_delete">
        <input type="hidden" name="item_type" value="contacts">
        
        <div class="mb-4 bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAllContacts" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Select All</span>
                    </label>
                    <span id="selectedContactsCount" class="text-sm text-gray-600">0 selected</span>
                </div>
                <button type="submit" id="bulkDeleteContactsBtn" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed" 
                        disabled 
                        onclick="return confirm('Are you sure you want to delete the selected contacts? This action cannot be undone.')">
                    Delete Selected
                </button>
            </div>
        </div>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" disabled>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject & Message</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($contacts_result->num_rows > 0): while($row = $contacts_result->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="item_ids[]" value="<?php echo $row['id']; ?>" 
                                           class="contact-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['name']); ?><br>
                                    <span class="text-gray-500 text-xs"><?php echo htmlspecialchars($row['email']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['subject']); ?></p>
                                    <p class="w-96 whitespace-pre-wrap mt-1"><?php echo htmlspecialchars($row['message']); ?></p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="admin.php?page=messages&to=<?php echo urlencode($row['email']); ?>" class="text-blue-600 hover:text-blue-900 mr-3">Message</a>
                                    <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>"><input type="hidden" name="type" value="contact">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No contact messages found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function toggleForm(formId) {
    const evaluationForm = document.getElementById('addEvaluationForm');
    const contactForm = document.getElementById('addContactForm');
    const formToShow = document.getElementById(formId);

    // If the form we want to show is already visible, hide it.
    if (formToShow.style.display === 'block') {
        formToShow.style.display = 'none';
    } else {
        // Otherwise, hide both forms and then show the one we want.
        evaluationForm.style.display = 'none';
        contactForm.style.display = 'none';
        formToShow.style.display = 'block';
        formToShow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Bulk delete functionality for evaluations
document.addEventListener('DOMContentLoaded', function() {
    // Evaluations bulk select
    const selectAllEvaluations = document.getElementById('selectAllEvaluations');
    const evaluationCheckboxes = document.querySelectorAll('.evaluation-checkbox');
    const bulkDeleteEvaluationsBtn = document.getElementById('bulkDeleteEvaluationsBtn');
    const selectedEvaluationsCount = document.getElementById('selectedEvaluationsCount');

    if (selectAllEvaluations) {
        selectAllEvaluations.addEventListener('change', function() {
            evaluationCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateEvaluationsBulkUI();
        });
    }

    evaluationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateEvaluationsBulkUI);
    });

    function updateEvaluationsBulkUI() {
        const checkedCount = document.querySelectorAll('.evaluation-checkbox:checked').length;
        const totalCount = evaluationCheckboxes.length;
        
        selectedEvaluationsCount.textContent = `${checkedCount} selected`;
        bulkDeleteEvaluationsBtn.disabled = checkedCount === 0;
        
        // Update select all checkbox state
        if (checkedCount === 0) {
            selectAllEvaluations.indeterminate = false;
            selectAllEvaluations.checked = false;
        } else if (checkedCount === totalCount) {
            selectAllEvaluations.indeterminate = false;
            selectAllEvaluations.checked = true;
        } else {
            selectAllEvaluations.indeterminate = true;
        }
    }

    // Contacts bulk select
    const selectAllContacts = document.getElementById('selectAllContacts');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const bulkDeleteContactsBtn = document.getElementById('bulkDeleteContactsBtn');
    const selectedContactsCount = document.getElementById('selectedContactsCount');

    if (selectAllContacts) {
        selectAllContacts.addEventListener('change', function() {
            contactCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateContactsBulkUI();
        });
    }

    contactCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateContactsBulkUI);
    });

    function updateContactsBulkUI() {
        const checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
        const totalCount = contactCheckboxes.length;
        
        selectedContactsCount.textContent = `${checkedCount} selected`;
        bulkDeleteContactsBtn.disabled = checkedCount === 0;
        
        // Update select all checkbox state
        if (checkedCount === 0) {
            selectAllContacts.indeterminate = false;
            selectAllContacts.checked = false;
        } else if (checkedCount === totalCount) {
            selectAllContacts.indeterminate = false;
            selectAllContacts.checked = true;
        } else {
            selectAllContacts.indeterminate = true;
        }
    }
});
</script>
