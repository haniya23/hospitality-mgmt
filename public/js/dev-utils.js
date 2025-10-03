// Development Banner Utility
window.devUtils = {
    showBanner: function() {
        window.resetBanner();
        console.log("Testing banner has been reset and will show on next page load");
    },
    hideBanner: function() {
        localStorage.setItem("bannerDismissed", "true");
        document.querySelector(".testing-banner")?.style.display = "none";
        window.toggleBannerVisibility();
        console.log("Testing banner has been hidden");
    },
    resetBanner: function() {
        window.resetBanner();
    }
};

console.log("Development utilities loaded! Use devUtils.showBanner() or devUtils.hideBanner() to control the banner");
