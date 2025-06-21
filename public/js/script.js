// Function to open a specific tab and hide others
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;

    // Get all elements with class="tab-content" and hide them
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tab-button" and remove the class "active"
    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab content, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Automatically display the first active tab on page load
document.addEventListener("DOMContentLoaded", function() {
    // Try to find the tab button already marked as active
    var activeTab = document.querySelector('.tab-button.active');
    if (activeTab) {
        activeTab.click();
    } else {
        // If no tab is marked active, click the first tab button
        var firstTab = document.querySelector('.tab-button');
        if (firstTab) {
            firstTab.click();
        }
    }
});
