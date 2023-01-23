import { Api } from "@/api/api";

class Renting extends Api {
  constructor() {
    super();
    this.path = "/rentings";
  }

  async findAll({ page = 1, itemsPerPage = 20, filters = [], orders = {} }) {
    return await this.get({path: this.path, page, itemsPerPage, filters, orders});
  }

  async findOne(id) {
    return await this.get({path: `${this.path}/${id}`});
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

export { Renting };
