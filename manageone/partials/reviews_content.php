<!-- Reviews Management Content -->
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg class="w-8 h-8 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Review Management
                </h1>
                <p class="text-gray-600 mt-1">Manage customer reviews and ratings for your website analysis service</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="toggleAddReviewForm()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                    + Add Review
                </button>
                <a href="../index.php" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                    View Live Site
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yellow-100">Average Rating</p>
                    <p class="text-2xl font-bold"><?php echo $reviewStats['average']; ?>/5</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-100">Total Reviews</p>
                    <p class="text-2xl font-bold"><?php echo $reviewStats['real_count'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-100">Pending Approval</p>
                    <p class="text-2xl font-bold"><?php echo count($pendingReviews); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-400 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-purple-100">Featured Reviews</p>
                    <p class="text-2xl font-bold"><?php echo $reviewStats['featured_count'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-indigo-100">Public Display</p>
                    <p class="text-2xl font-bold"><?php echo $reviewStats['count']; ?>+</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Review Form (Hidden by default) -->
    <div id="addReviewForm" class="bg-white shadow rounded-lg p-6 hidden">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Add New Review
            </h2>
            <button onclick="toggleAddReviewForm()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="admin.php?page=reviews" method="POST" class="space-y-6" id="addReviewFormElement">
            <input type="hidden" name="action" value="add_review">
            <input type="hidden" name="form_id" value="add_review_form">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                    <input type="text" id="customer_name" name="customer_name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">Customer Email</label>
                    <input type="email" id="customer_email" name="customer_email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div>
                <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                <input type="url" id="website_url" name="website_url" placeholder="https://example.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                <select id="rating" name="rating" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="5" selected>⭐⭐⭐⭐⭐ (5 stars)</option>
                    <option value="4">⭐⭐⭐⭐☆ (4 stars)</option>
                    <option value="3">⭐⭐⭐☆☆ (3 stars)</option>
                    <option value="2">⭐⭐☆☆☆ (2 stars)</option>
                    <option value="1">⭐☆☆☆☆ (1 star)</option>
                </select>
            </div>
            
            <div>
                <label for="review_text" class="block text-sm font-medium text-gray-700 mb-2">Review Text *</label>
                <textarea id="review_text" name="review_text" rows="4" required 
                          placeholder="Enter the customer's review..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>
            
            <div class="flex items-center space-x-6">
                <label class="flex items-center">
                    <input type="checkbox" name="approved" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Approve immediately</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="featured" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Mark as featured</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="toggleAddReviewForm()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Add Review
                </button>
            </div>
        </form>
    </div>

    <!-- Pending Reviews Section -->
    <?php if (!empty($pendingReviews)): ?>
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Pending Reviews (<?php echo count($pendingReviews); ?>)
            </h2>
        </div>
        <div class="divide-y divide-gray-200">
            <?php foreach ($pendingReviews as $review): ?>
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($review['customer_name']); ?></h3>
                            <div class="flex items-center space-x-2">
                                <div class="text-yellow-400 text-lg">
                                    <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                                </div>
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    <?php echo $review['rating']; ?>/5
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div><strong>Email:</strong> <?php echo htmlspecialchars($review['customer_email']); ?></div>
                                <div><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($review['created_at'])); ?></div>
                                <div><strong>Website:</strong> 
                                    <a href="<?php echo htmlspecialchars($review['website_url']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($review['website_url']); ?>
                                    </a>
                                </div>
                                <?php if ($review['report_id']): ?>
                                <div><strong>Report ID:</strong> <?php echo htmlspecialchars($review['report_id']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($review['review_text'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <p class="text-gray-700 italic">"<?php echo nl2br(htmlspecialchars($review['review_text'])); ?>"</p>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-center justify-between">
                            <form method="post" class="flex items-center space-x-4">
                                <input type="hidden" name="action" value="update_review">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                
                                <label class="flex items-center text-sm text-gray-600">
                                    <input type="checkbox" name="featured" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 mr-2">
                                    Mark as Featured
                                </label>
                                
                                <button type="submit" name="status" value="approve" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                                    ✅ Approve
                                </button>
                                
                                <button type="submit" name="status" value="reject" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150">
                                    ❌ Reject
                                </button>
                            </form>
                            
                            <!-- Quick Delete -->
                            <form method="post" class="ml-4" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                                <input type="hidden" name="action" value="manage_review">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <input type="hidden" name="management_action" value="delete">
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white shadow rounded-lg p-8 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a2 2 0 012-2h2.764"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No pending reviews</h3>
        <p class="mt-1 text-sm text-gray-500">All caught up! New reviews will appear here for approval.</p>
    </div>
    <?php endif; ?>

    <!-- All Reviews Management Section -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    All Reviews Management (<?php echo count($allReviews); ?>)
                </h2>
                
                <!-- Sorting Controls -->
                <div class="flex items-center space-x-4">
                    <select onchange="window.location.href='admin.php?page=reviews&order_by=' + this.value + '&order=<?php echo $order; ?>'" class="text-sm border-gray-300 rounded-md">
                        <option value="created_at" <?php echo $orderBy === 'created_at' ? 'selected' : ''; ?>>Date</option>
                        <option value="rating" <?php echo $orderBy === 'rating' ? 'selected' : ''; ?>>Rating</option>
                        <option value="customer_name" <?php echo $orderBy === 'customer_name' ? 'selected' : ''; ?>>Name</option>
                        <option value="approved" <?php echo $orderBy === 'approved' ? 'selected' : ''; ?>>Status</option>
                    </select>
                    <a href="admin.php?page=reviews&order_by=<?php echo $orderBy; ?>&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>" 
                       class="text-sm text-blue-600 hover:text-blue-800">
                        <?php echo $order === 'ASC' ? '↓' : '↑'; ?>
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (!empty($allReviews)): ?>
        <form method="post" id="bulkActionForm">
            <input type="hidden" name="action" value="bulk_action">
            
            <!-- Bulk Actions Bar -->
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center text-sm text-gray-700">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 mr-2">
                            Select All
                        </label>
                        <span id="selectedCount" class="text-sm text-gray-500">0 selected</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <select name="bulk_action_type" class="text-sm border-gray-300 rounded-md" required>
                            <option value="">Select Action</option>
                            <option value="approve">Approve</option>
                            <option value="unapprove">Unapprove</option>
                            <option value="feature">Feature</option>
                            <option value="unfeature">Unfeature</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150" 
                                onclick="return confirmBulkAction()">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Table -->
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                <?php foreach ($allReviews as $review): ?>
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-4">
                        <!-- Checkbox -->
                        <input type="checkbox" name="review_ids[]" value="<?php echo $review['id']; ?>" 
                               class="review-checkbox rounded border-gray-300 mt-1">
                        
                        <!-- Review Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($review['customer_name']); ?></h4>
                                    
                                    <!-- Status Badges -->
                                    <?php if ($review['approved']): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ Approved
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            ⏳ Pending
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($review['featured']): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⭐ Featured
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-yellow-400">
                                        <?php echo str_repeat('★', $review['rating']); ?>
                                    </div>
                                    <span class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600 mb-2">
                                <?php echo htmlspecialchars($review['customer_email']); ?> • 
                                <a href="<?php echo htmlspecialchars($review['website_url']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <?php echo htmlspecialchars($review['website_url']); ?>
                                </a>
                            </div>
                            
                            <?php if (!empty($review['review_text'])): ?>
                            <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md mb-3">
                                "<?php echo htmlspecialchars(substr($review['review_text'], 0, 150)) . (strlen($review['review_text']) > 150 ? '...' : ''); ?>"
                            </div>
                            <?php endif; ?>
                            
                            <!-- Quick Actions -->
                            <div class="flex items-center space-x-4 text-sm">
                                <form method="post" class="inline" onsubmit="return confirm('Toggle approval status?')">
                                    <input type="hidden" name="action" value="manage_review">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <input type="hidden" name="management_action" value="toggle_approval">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <?php echo $review['approved'] ? '🚫 Unapprove' : '✅ Approve'; ?>
                                    </button>
                                </form>
                                
                                <form method="post" class="inline" onsubmit="return confirm('Toggle featured status?')">
                                    <input type="hidden" name="action" value="manage_review">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <input type="hidden" name="management_action" value="toggle_featured">
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 font-medium">
                                        <?php echo $review['featured'] ? '⭐ Unfeature' : '⭐ Feature'; ?>
                                    </button>
                                </form>
                                
                                <form method="post" class="inline" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                                    <input type="hidden" name="action" value="manage_review">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <input type="hidden" name="management_action" value="delete">
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </form>
        
        <?php else: ?>
        <div class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet</h3>
            <p class="mt-1 text-sm text-gray-500">Reviews will appear here as customers submit them.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Approved Reviews -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Recent Approved Reviews
            </h2>
        </div>
        
        <?php if (!empty($approvedReviews)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <?php foreach (array_slice($approvedReviews, 0, 6) as $review): ?>
            <div class="bg-gradient-to-br from-white to-slate-50 rounded-xl shadow-lg border border-slate-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 min-h-[280px] flex flex-col">
                <!-- Header with Rating and Featured Badge -->
                <div class="p-4 border-b border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-yellow-400 text-lg">
                            <?php echo str_repeat('★', $review['rating']); ?>
                        </div>
                        <?php if (!empty($review['featured'])): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200">
                            ⭐ Featured
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-sm text-slate-500"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></div>
                </div>
                
                <!-- Review Text -->
                <?php if (!empty($review['review_text'])): ?>
                <div class="flex-1 p-4">
                    <blockquote class="text-slate-700 text-sm leading-relaxed italic">
                        "<?php echo htmlspecialchars(substr($review['review_text'], 0, 150)) . (strlen($review['review_text']) > 150 ? '...' : ''); ?>"
                    </blockquote>
                </div>
                <?php else: ?>
                <div class="flex-1 p-4 flex items-center justify-center">
                    <span class="text-slate-400 text-sm italic">No review text provided</span>
                </div>
                <?php endif; ?>
                
                <!-- Customer Info at Bottom -->
                <div class="p-4 bg-slate-50 rounded-b-xl border-t border-slate-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                            <?php echo strtoupper(substr($review['customer_name'], 0, 1)); ?>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($review['customer_name']); ?></p>
                            <p class="text-xs text-slate-600"><?php echo $review['rating']; ?>/5 Rating</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($approvedReviews) > 6): ?>
        <div class="px-6 py-4 bg-slate-50 text-center border-t border-slate-200 rounded-b-lg">
            <p class="text-sm text-slate-600">Showing 6 of <?php echo count($approvedReviews); ?> approved reviews</p>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No approved reviews yet</h3>
            <p class="mt-1 text-sm text-gray-500">Approved reviews will appear here and be displayed on your website.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleAddReviewForm() {
    const form = document.getElementById('addReviewForm');
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        form.classList.add('hidden');
    }
}
</script>