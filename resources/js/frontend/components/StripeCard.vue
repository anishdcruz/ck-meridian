<template>
	<div>
		<div id="card-element" class="form-control"></div>
		<div id="card-errors" role="alert"></div>
	</div>
</template>
<script>
	export default {
		props: {
			apiKey: String,
			form: String
		},
		mounted() {
			let stripe = Stripe(this.apiKey)

			var style = {
			  base: {
			    // Add your base input styles here. For example:
			    fontSize: '16px',
			    color: "#32325d",
			  }
			};

			let elements = stripe.elements()
			var card = elements.create('card', {
				style: style,
				hidePostalCode: true
			})
			card.mount('#card-element')

			card.addEventListener('change', (event) => {
			  var displayError = document.getElementById('card-errors');
			  if (event.error) {
			    displayError.textContent = event.error.message;
			  } else {
			    displayError.textContent = '';
			  }
			});

			var form = document.getElementById(this.form);
			form.addEventListener('submit', (event) => {
			  event.preventDefault();

			  stripe.createToken(card).then((result) => {
			    if (result.error) {
			      // Inform the customer that there was an error.
			      var errorElement = document.getElementById('card-errors');
			      errorElement.textContent = result.error.message;
			    } else {
			      // Send the token to your server.
			      this.stripeTokenHandler(result.token);
			    }
			  })
			})
		},
		methods: {
			stripeTokenHandler(token) {
				var form = document.getElementById(this.form);
			  var hiddenInput = document.createElement('input');
			  hiddenInput.setAttribute('type', 'hidden');
			  hiddenInput.setAttribute('name', 'stripeToken');
			  hiddenInput.setAttribute('value', token.id);
			  form.appendChild(hiddenInput);

			  // Submit the form
			  form.submit();
			}
		}
	}
</script>