/* Basic Declarations */
const pageLayer = document.getElementById("layer");

const headerWrapper = document.getElementsByClassName("header-wrapper")[0];

const menuBtn = document.getElementById("menu-btn");

const closeMenuBtn = document.getElementById("close-menu");

const searchBtn = document.getElementById("search-btn");

const searchSection = document.getElementById("search-container");

let searchSectionForm = null;

if (searchSection)
    searchSectionForm = searchSection.getElementsByClassName("content-form")[0];

const generalCategoriesBtn = document.getElementById("general-categories-btn");

const generalCategoriesSection = document.getElementsByClassName("general-categories-section")[0];

let otherCategories = null;

if (generalCategoriesSection)
    otherCategories = generalCategoriesSection.getElementsByClassName("content-form")[0];

const closeCategoriesForm = document.getElementById("close-categories-form");

const closeSearchForm = document.getElementById("close-search-form");

const filterSection = document.getElementsByClassName("filter-section")[0];

const filterBtn = document.getElementById("filter-btn");

const closeFilterSection = document.getElementById("close-filter-section");

const forgotPasswordBtn = document.getElementById("forgot-password-btn");

/* Main Page Navigation Actions */
menuBtn.onclick = () => {

    pageLayer.style.visibility = "visible";

    pageLayer.style.opacity = "1";

    headerWrapper.style.left = "0";

};

closeMenuBtn.onclick = () => {

    pageLayer.style.visibility = "hidden";

    pageLayer.style.opacity = "0";

    headerWrapper.style.left = (-headerWrapper.offsetWidth) + "px";

};

/* Courses Page Search Actions */
if (searchBtn) {

    searchBtn.onclick = () => {

        pageLayer.style.visibility = "visible";

        pageLayer.style.opacity = "1";

        searchSection.style.visibility = "visible";

        setTimeout(() => {

            searchSectionForm.style.top = "0";

            searchSectionForm.style.opacity = "1";

        }, 100);

    };

    closeSearchForm.onclick = () => {

        searchSectionForm.style.top = "100px";

        searchSectionForm.style.opacity = "0";

        setTimeout(() => {

            searchSection.style.visibility = "hidden";

            pageLayer.style.visibility = "hidden";

            pageLayer.style.opacity = "0";

        }, 250);

    };

}

/* Courses Page Filter Actions */
if (filterBtn) {

    filterBtn.onclick = () => {

        pageLayer.style.visibility = "visible";

        pageLayer.style.opacity = "1";

        filterSection.style.visibility = "visible";

        setTimeout(() => {

            filterSection.style.top = "50%";

            filterSection.style.opacity = "1";

        }, 100);

    };

    closeFilterSection.onclick = () => {

        filterSection.style.top = "70%";

        filterSection.style.opacity = "0";

        setTimeout(() => {

            filterSection.style.visibility = "hidden";

            pageLayer.style.visibility = "hidden";

            pageLayer.style.opacity = "0";

        }, 250);

    };

}

/* Courses Page General Categories Actions */
if (generalCategoriesBtn) {

    generalCategoriesBtn.onclick = () => {

        pageLayer.style.visibility = "visible";

        pageLayer.style.opacity = "1";

        generalCategoriesSection.style.visibility = "visible";

        setTimeout(() => {

            otherCategories.style.top = "0";

            otherCategories.style.opacity = "1";

        }, 100);

    };

    closeCategoriesForm.onclick = () => {

        otherCategories.style.top = "100px";

        otherCategories.style.opacity = "0";

        setTimeout(() => {

            generalCategoriesSection.style.visibility = "hidden";

            pageLayer.style.visibility = "hidden";

            pageLayer.style.opacity = "0";

        }, 250);

    };

}

/* Function to show a section and hide another by ID */
function ShowFieldContainer(elementToShow, elementToHide) {

    document.getElementById(elementToShow).style.display = "";

    document.getElementById(elementToHide).style.display = "none";

    setTimeout(function () {

        scrollToElement(elementToShow);

    }, 100);

}

/* Function to scroll to an element smoothly */
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

}


/* Forgot Password Section */
if (forgotPasswordBtn) {

    forgotPasswordBtn.onclick = () => {



    };

}