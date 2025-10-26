    //------------------------------SLIDER
    document.addEventListener("DOMContentLoaded", function () {
    const imgItem = document.querySelectorAll(".aspect-ratio-169 img")
    const imgItemContainer = document.querySelector(".aspect-ratio-169")
    const dotItem = document.querySelectorAll(".dot")
    
    // Check if elements exist before proceeding
    if (!imgItemContainer || imgItem.length === 0 || dotItem.length === 0) {
        // Slider elements not found on this page, skip initialization
        return;
    }
    
    let index = 0;
    let imgLeng = imgItem.length
    
    imgItem.forEach(function(image,index){
        if (image && image.style) {
            image.style.left = index*100 + "%"
        }
        if (dotItem[index]) {
            dotItem[index].addEventListener("click",function(){
                slideRun (index)
            })
        }
    })
    
    function slider (){
        index++;
        if(index >= imgLeng){
            index=0;
        }
        slideRun (index)
    }
    
    function slideRun (index) {
        if (imgItemContainer && imgItemContainer.style) {
            imgItemContainer.style.left = "-" + index*100 + "%"
        }
        
        let dotActive = document.querySelector(".active")
        if (dotActive) {
            dotActive.classList.remove("active")
        }
        if (dotItem[index]) {
            dotItem[index].classList.add("active");
        }
    }

    setInterval (slider,5000)
}); 