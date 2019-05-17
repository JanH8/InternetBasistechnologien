function Blutalkoholkonzentration(getr, alkgeh, gew, gender){
    const a = getr*alkgeh * 0.81;
    const b = gew * gender;
    return a / b;
}