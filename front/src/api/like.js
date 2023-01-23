import { Api } from "@/api/api";

class Like extends Api {
  constructor() {
    super();
    this.path = "/likes";
  }

  async findAll({page = 1, itemsPerPage = 20, filters = [], orders = {}}) {
    return await this.get({path: this.path, page, itemsPerPage, filters, orders});
  }

  async findOne(id) {
    return await this.get({path: `${this.path}/${id}`, page: null, itemsPerPage: null});
  }

  async create(data) {
    return await this.post(this.path, data);
  }

  async update(id, data) {
    return await this.put(`${this.path}/${id}`, data);
  }

  async delete(id) {
    return await this.delete(`${this.path}/${id}`);
  }
}

export { Like };
