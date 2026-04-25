import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Index({ materials }) {
    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight">Materials</h2>}
        >
            <Head title="Materials" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-end mb-4">
                        <Link
                            href={route('materials.create')}
                            className="px-4 py-2 bg-blue-600 text-white rounded"
                        >
                            New material
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th className="px-6 py-3 text-left">ID</th>
                                    <th className="px-6 py-3 text-left">Name</th>
                                    <th className="px-6 py-3 text-left">Price</th>
                                    <th className="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {materials.data.map((m) => (
                                    <tr key={m.id}>
                                        <td className="px-6 py-4">{m.id}</td>
                                        <td className="px-6 py-4">{m.name}</td>
                                        <td className="px-6 py-4">{m.price}</td>
                                        <td className="px-6 py-4 text-right text-sm">
                                            <Link
                                                href={route('materials.edit', m.id)}
                                                className="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="p-4">
                            {materials.links && (
                                <div dangerouslySetInnerHTML={{ __html: materials.links }} />
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}