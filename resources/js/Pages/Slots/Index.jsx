import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Index({ slots }) {
    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight">Slots</h2>}
        >
            <Head title="Slots" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-end mb-4">
                        <Link
                            href={route('slots.create')}
                            className="px-4 py-2 bg-blue-600 text-white rounded"
                        >
                            New slot
                        </Link>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th className="px-6 py-3 text-left">ID</th>
                                    <th className="px-6 py-3 text-left">Total bricks</th>
                                    <th className="px-6 py-3 text-left">Start</th>
                                    <th className="px-6 py-3 text-left">End</th>
                                    <th className="px-6 py-3 text-left">Status</th>
                                    <th className="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {slots.data.map((slot) => (
                                    <tr key={slot.id}>
                                        <td className="px-6 py-4 whitespace-nowrap">{slot.id}</td>
                                        <td className="px-6 py-4 whitespace-nowrap">{slot.total_bricks}</td>
                                        <td className="px-6 py-4 whitespace-nowrap">{slot.start_date}</td>
                                        <td className="px-6 py-4 whitespace-nowrap">{slot.end_date}</td>
                                        <td className="px-6 py-4 whitespace-nowrap">{slot.status}</td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <Link
                                                href={route('slots.show', slot.id)}
                                                className="text-blue-600 hover:text-blue-900 mr-2"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                href={route('slots.edit', slot.id)}
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
                            {/* simple pagination links */}
                            {slots.links && (
                                <div
                                    dangerouslySetInnerHTML={{ __html: slots.links }}
                                />
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
