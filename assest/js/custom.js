/*-------------------------------------  Scroll Bottom to top Event-------------------------------------*/
 $(window).scroll(function() 
 {
  if ($(this).scrollTop() >= 50) {       
    $('.scroll-top').fadeIn(200);    
  } else {
    $('.scroll-top').fadeOut(200);   
  }
});
 $('.scroll-top').click(function() {      
  $('body,html').animate({
    scrollTop : 0                       
  }, 500);
});

/*-------------- Typing ------------------*/
const typedTextSpan = document.querySelector(".typed-text");
const cursorSpan = document.querySelector(".cursor");
const textArray = ["Agency Service Invoice","Hotel Booking Invoice","Restaurant Bill Invoice","Bus Booking Invoice","Money Exchange Invoice","Hospital or Medical Invoice","Movie Booking Invoice","Stadium Seat Invoice","Flight Booking Invoice","Car Booking Invoice","Train Booking Invoice","eCommerce Bill Invoice","Student Billing Invoice","Domain & Hosting Invoice","Internet Bill Invoice","Coffee Shop Invoice","Travel Invoice","Fitness Invoice","Cleaning Service Invoice","Photostudio Invoice"];
const typingDelay = 100;
const erasingDelay = 100;
const newTextDelay = 1000;
let textArrayIndex = 0;
let charIndex = 0;
function type() {
  if (charIndex < textArray[textArrayIndex].length) {
    if(!cursorSpan.classList.contains("typing")) cursorSpan.classList.add("typing");
    typedTextSpan.textContent += textArray[textArrayIndex].charAt(charIndex);
    charIndex++;
    setTimeout(type, typingDelay);
  } 
  else {
    cursorSpan.classList.remove("typing");
    setTimeout(erase, newTextDelay);
  }
}
function erase() {
  if (charIndex > 0) {
    if(!cursorSpan.classList.contains("typing")) cursorSpan.classList.add("typing");
    typedTextSpan.textContent = textArray[textArrayIndex].substring(0, charIndex-1);
    charIndex--;
    setTimeout(erase, erasingDelay);
  } 
  else {
    cursorSpan.classList.remove("typing");
    textArrayIndex++;
    if(textArrayIndex>=textArray.length) textArrayIndex=0;
    setTimeout(type, typingDelay + 100);
  }
}
document.addEventListener("DOMContentLoaded", function() {
  if(textArray.length) setTimeout(type, newTextDelay + 250);
});






