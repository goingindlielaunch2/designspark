<?php
// File: partials/messages_content.php
// =================================================================
// This file contains the HTML and PHP for the messaging center.
// =================================================================
?>
<h1 class="text-3xl font-bold text-gray-900 mb-6">Send a Message</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Single Message Form -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Send a Single Email</h2>
        <form action="send_email.php" method="POST">
            <input type="hidden" name="type" value="single">
            <div class="mb-4">
                <label for="to_email" class="block text-sm font-medium text-gray-700">To Email Address</label>
                <input type="email" name="to_email" id="to_email" required value="<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" id="subject" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="message" rows="6" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                    Send Message
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Message Form -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Send a Bulk Email</h2>
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded-md">
            <p class="font-bold">Target Audience</p>
            <p>This message will be sent to all <strong><?php echo $newsletter_subscribers_count; ?></strong> users who subscribed to the newsletter.</p>
        </div>
        <form action="send_email.php" method="POST" onsubmit="return confirm('Are you sure you want to send this bulk message?');">
            <input type="hidden" name="type" value="bulk">
            <div class="mb-4">
                <label for="bulk_subject" class="block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" id="bulk_subject" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="bulk_message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="bulk_message" rows="6" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
                    Send Bulk Message
                </button>
            </div>
        </form>
    </div>
</div>
