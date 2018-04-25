function toggleForm(formID) {
	var formIDEle = document.getElementById(formID);
	if (formIDEle.style.display === "none") {
		formIDEle.style.display = "block";
	} else {
		formIDEle.style.display = "none";
	}
}