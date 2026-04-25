import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ slot }) {
    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight">Slot #{slot.id}</h2>}
        >
            <Head title={`Slot ${slot.id}`} />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white p-6 rounded shadow">
                        <dl className="grid grid-cols-2 gap-x-4 gap-y-2">
                            <dt className="font-medium">Total bricks</dt>
                            <dd>{slot.total_bricks}</dd>

                            <dt className="font-medium">Start date</dt>
                            <dd>{slot.start_date}</dd>

                            <dt className="font-medium">End date</dt>
                            <dd>{slot.end_date}</dd>

                            <dt className="font-medium">Status</dt>
                            <dd>{slot.status}</dd>
                        </dl>

                        <div className="mt-6">
                            <h3 className="font-semibold">Materials</h3>
                            <ul className="list-disc ml-6">
                                {slot.materials.map((m) => (
                                    <li key={m.id}>
                                        {m.name} &ndash; qty: {m.pivot.quantity} &ndash; price:{' '}
                                        {m.pivot.price} &ndash; added:{' '}
                                        {m.pivot.added_at}
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <div className="mt-6">
                            <h3 className="font-semibold">Workers</h3>
                            <ul className="list-disc ml-6">
                                {slot.workers.map((w) => (
                                    <li key={w.id}>
                                        {w.name} &ndash; start: {w.pivot.start_time} &ndash; end:{' '}
                                        {w.pivot.end_time} &ndash; amount: {w.pivot.amount}
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <div className="mt-6">
                            <Link
                                href={route('slots.index')}
                                className="text-gray-600 hover:text-gray-900"
                            >
                                Back to list
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
