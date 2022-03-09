function showConfirm(){
    var answer = confirm("Naozaj chcete vymazať daný záznam?");

    if(answer){
        //vykonanie, ak pouzivatel potvrdi confirm okno - odkaz sa vykona
        return true;
    }else{
        //vykonanie, ak pouzivatel zruzi confirm okno - odkaz sa nevykona
        return false;
    }
}