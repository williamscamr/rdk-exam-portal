const validation = new JustValidate("#signup");

validation
	.addField("#username", [
	{
		rule: "required"
	},
	{
		validator: (value) => () => {
			return fetch("validate-username.php?username=" + encodeURIComponent(value))
			.then(function(response) {
				return response.json();
			})
			.then(function(json) {
				return json.available;
			});
		},
		errorMessage: "username already taken"
	}
	])
	.addField("#firstname", [
	{
		rule: "required"
	},
	])
	.addField("#lastname", [
	{
		rule: "required"
	},
	])
	.addField("#email", [
	{
		rule: "required"
	},
	{
		rule: "email"
	},/*
	{
		validator: (value) => () => {
			return fetch("validate-email.php?email=" + encodeURIComponent(value))
			.then(function(response) {
				return response.json();
			})
			.then(function(json) {
				return json.available;
			});
		},
		errorMessage: "email already taken"
	},*/
	])
	/*
	.addField("#belt", [
	{
		rule: "required"
	},
	])*/
	.addRequiredGroup("#belt_radio_group", "Select at least one option!")
	//.addRequiredGroup("studio", "Select at least one option", {
  //successMessage: "Everything looks good",
	//})
	
	.addField("#studio", [
	{
		rule: "required"
	},
	])
	.addField("#password", [
	{
		rule: "required"
	},
	{
		rule: "strongPassword"
	},
	])
	.addField("#password_confirmation", [
	{
		validator: (value, fields) => {
			
			//return value === fields["#password"].value;
			return value === fields["#password"].elem.value;
		},
		errorMessage: "Passwords should match"
	},
	])
	.onSuccess((event) => {
		event.preventDefault();
		document.getElementById("signup").submit();
	});
	