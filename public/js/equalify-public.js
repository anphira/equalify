function generate_sitemap() {
	const textarea = document.getElementById('urlTextarea');
    const output = document.getElementById('output');

    // Clear previous output
    output.innerHTML = '';

    // Get URLs from the textarea
    const urls = textarea.value.trim().split('\n');

    // Validate that each line is a valid URL
    const validUrls = urls.filter(url => isValidUrl(url));
    const invalidUrls = urls.filter(url => !isValidUrl(url));

    if (invalidUrls.length > 0) {
        output.innerHTML = `<p style="color: red;">Invalid URLs found: ${invalidUrls.length}. Please ensure all entries are valid URLs.</p>`;
        return;
    }

    // Report the count of valid URLs
    output.innerHTML = `<p style="color: green;">Total valid URLs: ${validUrls.length}</p><p>Copy the below XML and save as an XML file. Upload the XML file to a publicly accessible web location. Make note of the URL that you uploaded it to.</p>`;

    // Generate XML sitemap
    const xmlSitemap = generateSitemap(validUrls);

    // Display the XML sitemap
    const pre = document.createElement('pre');
    pre.textContent = xmlSitemap;
    output.appendChild(pre);
}

// Helper function to validate a URL
function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch (e) {
        return false;
    }
}

// Helper function to generate XML sitemap
function generateSitemap(urls) {
    const header = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n`;
    const footer = `</urlset>`;
    const urlEntries = urls.map(url => `  <url>\n    <loc>${url}</loc>\n  </url>`).join('\n');
    return header + urlEntries + '\n' + footer;
}