// include jquery to make an easier ajax requests
import 'https://code.jquery.com/jquery-3.7.1.min.js';

// Get the modal
var modal = document.getElementById("myModal");
var formBtn = document.querySelector("#submit-btn");
var urlInput = document.querySelector("#url-input");
var htmlElementInput = document.querySelector("#html-element-input");
var errorBlock = document.querySelector(".modal .error-block");
var modalContentBlock = document.querySelector(".modal .content-block")

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

// button submit handler
formBtn.addEventListener('click', (e) => {
    // prevent default actions
    e.preventDefault();

    // make modal window appear
    modal.style.display = "block";
    errorBlock.innerHTML = "";
    modalContentBlock.innerHTML = "";

    // get url and htlm element
    var url = urlInput.value + "";
    var htmlElement = htmlElementInput.value + "";
    
    // check if htmlElement is valid
    if (htmlElement.includes(">") || htmlElement.includes("<") || htmlElement.includes("/") || !htmlElement) {
        errorBlock.textContent = " html element should be not empty and be a simple tag without brackets, for example: img, NOT <img />";
        return;
    }

    var urlRegex = /^(http|https):\/\/[^ "]+$/;
    // check if url is valid
    if (!urlRegex.test(url)) {
        errorBlock.textContent = "url is invalid";
        return;
    }

    $.ajax({
        url: "/api/controllers/getPageController.php",
        type: "POST",
        data: {
            page_url: url,
            html_element: htmlElement
        }
    }).then((msg) => {
        let response = JSON.parse(msg);
        
        // if there is mistake message, show it
        if ("message" in response) {
            errorBlock.innerHTML = response['message'];
            return;
        }
        
        var pageStatistic = response.page_statistic;
        var generalStatistic = response.general_statistic;
        
        // as we don't use any frameworks here, I'll use the simple way to show the message
        modalContentBlock.innerHTML = `
                <h1>Page Statistic:</h1>
                <h3>URL: ${pageStatistic.url}</h3>
                <h3>Request Date: ${pageStatistic.request_date}</h3>
                <h3>Response Time: ${pageStatistic.response_time}ms</h3>
                <h3>Element Tag: ${pageStatistic.element_tag}</h3>
                <h3>Tag Count: ${pageStatistic.tag_count}</h3>

                <h1>General Statistic:</h1>
                <h3>i. ${generalStatistic.domain_urls_fetched} URLs of that domain have been checked </h3>
                <h3>ii. Average page fetch time from that domain during the last 24 hours: ${generalStatistic.today_domains_fetch_time}ms</h3>
                <h3>iii. Total count of this element from this domain: ${generalStatistic.domains_html_element_count}</h3>
                <h3>iv. Total count of this element from ALL requests ever made: ${generalStatistic.overall_html_elements_count}</h3>
        `;
    });
});
