export interface CategoryForm{
    name: string;
    description: string;
    image: File | null;
    imagePreview: string | null;
    _method:string|null;
}

export interface CategoryResource {
    id: number | null;
    name: string;
    slug: string | null;
    description: string;
    image_path: string;
    created_at: string;
    updated_at: string;
}

