function autofill(fnameId, lnameId, fullnameId) {
  const fname = document.getElementById(fnameId).value;
  const lname = document.getElementById(lnameId).value;

  // Concatenate first and last names with a space.
  const fullname = fname + ' ' + lname;

  // Update the full name field with the concatenated value.
  document.getElementById(fullnameId).value = fullname;
}
