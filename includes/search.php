<?php
// search_form.php (Revised version)

/**
 * A component for displaying a job search form.
 *
 * @param array $attributes Optional array of HTML attributes for the form element.
 */
function renderSearchForm(array $attributes = []): string
{
    // Set default action if not provided
    $attributes['action'] = $attributes['action'] ?? '';

    // Sanitize attributes
    $safeAttributes = array_map('htmlspecialchars', $attributes);

    // Build the HTML attribute string
    $attributeString = '';
    foreach ($safeAttributes as $key => $value) {
        $attributeString .= ' ' . $key . '="' . $value . '"';
    }

    $html = <<<HTML
               <div class="search-container" style="background: transparent">
                <form class="search-form">
                <div style="position: relative; flex-grow: 1; min-width: 250px;">
        <i class="fa fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 1rem;"></i>
        <input type="text" 
               placeholder="Job title or keyword" 
               style="width: 100%;
                      padding: 0.875rem 1rem 0.875rem 2.5rem;
                      border: 1px solid #e5e7eb;
                      border-radius: 0.5rem;
                      font-size: 1rem;
                      color: #374151;
                      background: #f9fafb;
                      transition: all 0.2s ease;">
    </div>

    <div style="position: relative; flex-grow: 1; min-width: 250px;">
        <i class="fa fa-map-marker" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #6b7280; font-size: 1rem;"></i>
        <input type="text" 
               placeholder="Location" 
               style="width: 100%;
                      padding: 0.875rem 1rem 0.875rem 2.5rem;
                      border: 1px solid #e5e7eb;
                      border-radius: 0.5rem;
                      font-size: 1rem;
                      color: #374151;
                      background: #f9fafb;
                      transition: all 0.2s ease;">
    </div>

                    <button type="submit" class="search-button">Search Jobs</button>
                </form>
            </div>
HTML;

    return $html;
}
?>