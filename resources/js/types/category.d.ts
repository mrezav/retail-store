export interface CategoryForm{
    name: string;
    description: string;
    image: File | null;
    imagePreview: string | null;
}

export interface CategoryResource {
    id: number;
    name: string;
    description: string;
    image_path: string;
}

