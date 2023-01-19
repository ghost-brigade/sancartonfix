import { Api } from "@/api/api";

class Security extends Api {
    constructor() {
      super();
      this.path = "/";
    }

    async token(data) {
        const returned = await this.post(`${this.path}authentication_token`, data);

        const token = returned.token;
        if (token) {
            localStorage.setItem("token", token);
        }

        return token;
    }

    async profile() {
        return await this.get(`${this.path}profile`);
    }

}

export { Security };