import { Api } from "@/api/api";

class Security extends Api {
    constructor() {
      super();
      this.authentication_path = '/authentication_token';
      this.profile_path        = '/profile';
    }

    async token(data) {
        try {
          const returned = await this.post(this.authentication_path, data);

          if (returned === null) {
            throw new Error("Error while getting token, please retry later");
          }

          const res = await returned?.json();

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
        try {
          return await this.get({path: this.profile_path}).then((res) => res.json());
        } catch (err) {
            console.error(err.message);
        }
    }

}

export { Security };
