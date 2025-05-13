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

document.addEventListener('DOMContentLoaded', function () {
    const productContainer = document.getElementById('product-container');
    const addButton = document.getElementById('add-product-btn');
    const totalField = document.getElementById('total');
    const subtotalField = document.getElementById('subtotal');

    // Calculate all totals
    async function updateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        // Get all product rows
        const rows = productContainer.querySelectorAll('.product-row');

        rows.forEach(row => {
            const amount = parseFloat(row.querySelector('.amount').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax').value) || 0;

            subtotal += amount;
            totalTax += (amount * taxRate / 100);
        });

        console.log("Total"+subtotal.toFixed(2));

        // Update display
        subtotalField.innerHTML = subtotal.toFixed(2);
        totalField.innerHTML = (subtotal + totalTax).toFixed(2);
    }

    // Calculate amount for a single row
    async function calculateAmount(row) {
        const unitPrice = parseFloat(row.querySelector('.unitPrice').value) || 0;
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const amount = (unitPrice * qty).toFixed(2);

        row.querySelector('.amount').value = amount;
        console.log("Amount"+amount.toFixed(2));
        updateTotals();
    }

    // Set up event listeners for a row
    async function attachRowEvents(row) {
        row.querySelector('.unitPrice').addEventListener('input', () => calculateAmount(row));
        row.querySelector('.qty').addEventListener('input', () => calculateAmount(row));
        row.querySelector('.tax').addEventListener('input', () => updateTotals());

        row.querySelector('.delete-btn').addEventListener('click', function() {
            if (productContainer.querySelectorAll('.product-row').length > 1) {
                row.remove();
                updateTotals();
            }
        });
    }

    // Add new product row
    addButton.addEventListener('click', function() {
        const newRow = productContainer.querySelector('.product-row').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        attachRowEvents(newRow);
        productContainer.appendChild(newRow);
    });

    // Initialize first row
    attachRowEvents(productContainer.querySelector('.product-row'));
});
