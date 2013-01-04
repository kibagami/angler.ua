function Person () {
    this.firstname = 'Stephen';
    this.secondname = 'King';
    this.count = 0;
}

Person.prototype.countCall = function  () {
    return this.count;
};

function Writer () {
    this.pseudoName = 'Horror';
}

Writer.prototype.getPseudoName = function  () {
    return this.pseudoName;
};


Person.prototype.countCall = function  () {
    this.count += 1;
};

function Student () {
    Student.superclass.constructor.call(this, arguments[0]);

    this.faculty = null;
    this.institute = '';
}
Student
    .extend(Person)
    .augment(Writer);

/**
 * @return {string}
 */
Student.prototype.getFullName = function  () {
    return this.firstname + " " + this.secondname;
};