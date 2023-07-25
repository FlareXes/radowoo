// Set your publishable key: remember to change this to your live publishable key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
var stripe = Stripe(stripe_publishable_key);
var elements = stripe.elements();
var clientSecret = $('input[name=client_secret]').val();
var txn_id = $('input[name=txn-id]').val();
var loged_in_user_name = $('input[name=name]').val();
var purchases_type = $('input[name=purchases_type]').val();
var quiz_id = $('input[name=quiz_id]').val();

// Set up Stripe.js and Elements to use in checkout form 
var elements = stripe.elements();
var style = {
  base: {
    color: "#32325d",
  }
};

var card = elements.create("card", { style: style });
card.mount("#card-element");

card.on('change', ({error}) => {
  let displayError = document.getElementById('card-errors');
  if (error) {
    displayError.textContent = error.message;
  } else {
    displayError.textContent = '';
  }
});

var form = document.getElementById('payment-form');

form.addEventListener('submit', function(ev) {
  ev.preventDefault();
  stripe.confirmCardPayment(clientSecret, {
    payment_method: {
      card: card,
      billing_details: {
        name: loged_in_user_name,
      }
    },
  }).then(function(result) {
    if (result.error) {
      // Show error to your customer (e.g., insufficient funds)
      console.log(result.error.message);
      $('#card-errors').text(result.error.message);
    } 
    else 
    {
      console.log(result.paymentIntent.status);
      // The payment has been processed!
      if (result.paymentIntent.status === 'succeeded') 
      {
        window.location = BASE_URL + "stripe/check-payment/"+purchases_type+"/"+quiz_id+"/"+txn_id; 
      }
    }
  });
});


      