import { Api } from "@/api/api";

class Security extends Api {
    constructor() {
      super();
      this.path = "/";
    }

    async token(data, jsonFormat = true) {
        try {
          const returned = await this.post(`${this.path}authentication_token`, data, jsonFormat);
          const res = await returned.json();

          if(returned.ok) {
            const token = res.token;

            if (token) {
              localStorage.setItem("token", token);
            }

            return token;
          }

          throw new Error(res.message);
        } catch (err) {
          throw new Error(err);
        }
    }

    async profile() {
        return await this.get(`${this.path}profile`, );
    }

}

export { Security };
