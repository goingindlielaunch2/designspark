-- Reviews table for DesignSpark website analysis
-- Run this in your MySQL database: rfvbydmy_designspark

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    website_url VARCHAR(255) NOT NULL,
    rating INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    report_id VARCHAR(32),
    session_id VARCHAR(255),
    approved BOOLEAN DEFAULT FALSE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_approved (approved),
    INDEX idx_featured (featured),
    INDEX idx_email (customer_email),
    INDEX idx_session (session_id)
);

-- Insert a sample approved review for testing
INSERT INTO reviews (customer_name, customer_email, website_url, rating, review_text, approved, featured, created_at) 
VALUES 
('Sarah Johnson', 'sarah@example.com', 'https://example.com', 5, 'This analysis was incredibly detailed and gave me actionable insights I could implement immediately. The UX recommendations helped increase our conversion rate by 23%!', 1, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
('Mike Chen', 'mike@startup.com', 'https://startup.com', 5, 'Best investment I made for my website. The report identified issues I never noticed and provided clear solutions.', 1, 0, DATE_SUB(NOW(), INTERVAL 7 DAY)),
('Lisa Rodriguez', 'lisa@business.com', 'https://business.com', 4, 'Very thorough analysis with practical recommendations. Helped me prioritize what to fix first.', 1, 0, DATE_SUB(NOW(), INTERVAL 10 DAY));
