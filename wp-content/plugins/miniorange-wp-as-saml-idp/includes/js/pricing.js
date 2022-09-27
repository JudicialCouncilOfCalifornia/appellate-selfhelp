//new pricing js
jQuery(document).ready(function () {
    $idp = jQuery;
    document.getElementById("mo-idp-price-head").style.display = "block";
    document.getElementById("mo-idp-price-head1").style.display = "block";
    document.getElementById("mo-idp-final-pricing").style.display = "none";
    let selection = document.getElementById("mo-idp-pricing-users");
    let fullPrice = document.getElementById("mo-idp-full-price");
    let halfPrice = document.getElementById("mo-idp-half-price");

    selection.addEventListener("change", () => {
        let selectedValue = selection.options[selection.selectedIndex];
        if (selectedValue.value == 1) {
            fullPrice.innerText = "$500";
            halfPrice.innerText = "$250";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 2) {
            fullPrice.innerText = "$600";
            halfPrice.innerText = "$300";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 3) {
            fullPrice.innerText = "$700";
            halfPrice.innerText = "$350";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 4) {
            fullPrice.innerText = "$800";
            halfPrice.innerText = "$400";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 5) {
            fullPrice.innerText = "$900";
            halfPrice.innerText = "$450";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 6) {
            fullPrice.innerText = "$1300";
            halfPrice.innerText = "$650";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 7) {
            fullPrice.innerText = "$2000";
            halfPrice.innerText = "$1000";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 8) {
            fullPrice.innerText = "$2600";
            halfPrice.innerText = "$1300";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 9) {
            fullPrice.innerText = "$3100";
            halfPrice.innerText = "$1550";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else if (selectedValue.value == 10) {
            fullPrice.innerText = "$3500";
            halfPrice.innerText = "$1750";
            document.getElementById("mo-idp-price-head").style.display = "block";
            document.getElementById("mo-idp-price-head1").style.display = "block";
            document.getElementById("mo-idp-final-pricing").style.display = "none";
            document.getElementById("sub-price-tr2").style.display = "block";
            document.getElementById("sub-price-tr3").style.display = "block";
            document.getElementById("idp-border1").style.borderBottom = "1px solid #dfdfdf";
            document.getElementById("idp-border2").style.borderBottom = "1px solid #dfdfdf"
        } else {
            document.getElementById("mo-idp-price-head").style.display = "none";
            document.getElementById("mo-idp-price-head1").style.display = "none";
            document.getElementById("mo-idp-final-pricing").style.display = "block";
            document.getElementById("sub-price-tr2").style.display = "none";
            document.getElementById("sub-price-tr3").style.display = "none";
            document.getElementById("idp-border1").style.borderBottom = "none";
            document.getElementById("idp-border2").style.borderBottom = "none";
        }
    });
});