export default class gate {
    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.type === 'admin';
    }

    isUser(){
        return this.user.type === 'user';
    }

    isAuthor(){
        return this.user.type === 'author';
    }
}
