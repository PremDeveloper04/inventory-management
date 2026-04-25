import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        price: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('materials.store'));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight">New Material</h2>}
        >
            <Head title="New Material" />
            <div className="py-12">
                <div className="max-w-md mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white p-6 rounded shadow">
                        <form onSubmit={submit}>
                            <div className="space-y-4">
                                <div>
                                    <label className="block font-medium">Name</label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded"
                                    />
                                    {errors.name && <div className="text-red-600">{errors.name}</div>}
                                </div>
                                <div>
                                    <label className="block font-medium">Price</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        value={data.price}
                                        onChange={(e) => setData('price', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded"
                                    />
                                    {errors.price && <div className="text-red-600">{errors.price}</div>}
                                </div>
                                <div className="mt-6">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-4 py-2 bg-blue-600 text-white rounded"
                                    >
                                        Save
                                    </button>
                                    <Link
                                        href={route('materials.index')}
                                        className="ml-4 text-gray-600"
                                    >
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
