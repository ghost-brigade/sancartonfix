import { Category } from "@/api/category";

const category = new Category();

const filters = [
  { property: "name", value: "CARTON" },
  { property: "name", value: "BANC" },
];

category
  .findAll({ page: 1, itemsPerPage: 20, filters: filters })
  .then((response) => {
    console.log(response);
  });
