import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';
import { Fragment, useEffect } from 'react';

export default function Edit({ slot, materials, workers }) {
    const { data, setData, put, processing, errors } = useForm({
        total_bricks: slot.total_bricks,
        start_date: slot.start_date,
        end_date: slot.end_date || '',
        status: slot.status,
        materials: slot.materials.map((m) => ({
            id: m.id,
            quantity: m.pivot.quantity,
            price: m.pivot.price,
            added_at: m.pivot.added_at,
        })),
        workers: slot.workers.map((w) => ({
            id: w.id,
            start_time: w.pivot.start_time,
            end_time: w.pivot.end_time,
            amount: w.pivot.amount,
        })),
    });

    const addMaterial = () => {
        setData('materials', [
            ...data.materials,
            { id: '', quantity: '', price: '', added_at: '' },
        ]);
    };

    const removeMaterial = (index) => {
        const list = [...data.materials];
        list.splice(index, 1);
        setData('materials', list);
    };

    const addWorker = () => {
        setData('workers', [
            ...data.workers,
            { id: '', start_time: '', end_time: '', amount: '' },
        ]);
    };

    const removeWorker = (index) => {
        const list = [...data.workers];
        list.splice(index, 1);
        setData('workers', list);
    };

    const submit = (e) => {
        e.preventDefault();
        put(route('slots.update', slot.id));
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight">Edit Slot</h2>}
        >
            <Head title="Edit Slot" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white p-6 rounded shadow">
                        <form onSubmit={submit}>
                            <div className="space-y-4">
                                <div>
                                    <label className="block font-medium">Total Bricks</label>
                                    <input
                                        type="number"
                                        value={data.total_bricks}
                                        onChange={(e) => setData('total_bricks', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded"
                                    />
                                    {errors.total_bricks && (
                                        <div className="text-red-600">{errors.total_bricks}</div>
                                    )}
                                </div>

                                <div>
                                    <label className="block font-medium">Start Date</label>
                                    <input
                                        type="date"
                                        value={data.start_date}
                                        onChange={(e) => setData('start_date', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded"
                                    />
                                    {errors.start_date && (
                                        <div className="text-red-600">{errors.start_date}</div>
                                    )}
                                </div>

                                <div>
                                    <label className="block font-medium">End Date</label>
                                    <input
                                        type="date"
                                        value={data.end_date}
                                        onChange={(e) => setData('end_date', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded"
                                    />
                                    {errors.end_date && (
                                        <div className="text-red-600">{errors.end_date}</div>
                                    )}
                                </div>

                                <div>
                                    <label className="block font-medium">Status</label>
                                    <input
                                        type="text"
                                        value={data.status}
                                        onChange={(e) => setData('status', e.target.value)}
                                        className="mt-1 block w-full border-gray-300 rounded"
                                    />
                                    {errors.status && (
                                        <div className="text-red-600">{errors.status}</div>
                                    )}
                                </div>

                                {/* materials section */}
                                <div className="mt-6">
                                    <h3 className="font-semibold">Materials</h3>
                                    {data.materials.map((mat, idx) => (
                                        <Fragment key={idx}>
                                            <div className="grid grid-cols-5 gap-2 items-center mt-2">
                                                <select
                                                    name="id"
                                                    value={mat.id}
                                                    onChange={(e) => {
                                                        const list = [...data.materials];
                                                        list[idx].id = e.target.value;
                                                        setData('materials', list);
                                                    }}
                                                    className="border-gray-300 rounded col-span-2"
                                                >
                                                    <option value="">--select--</option>
                                                    {materials.map((m) => (
                                                        <option key={m.id} value={m.id}>
                                                            {m.name}
                                                        </option>
                                                    ))}
                                                </select>
                                                <input
                                                    type="number"
                                                    placeholder="qty"
                                                    value={mat.quantity}
                                                    onChange={(e) => {
                                                        const list = [...data.materials];
                                                        list[idx].quantity = e.target.value;
                                                        setData('materials', list);
                                                    }}
                                                    className="border-gray-300 rounded"
                                                />
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    placeholder="price"
                                                    value={mat.price}
                                                    onChange={(e) => {
                                                        const list = [...data.materials];
                                                        list[idx].price = e.target.value;
                                                        setData('materials', list);
                                                    }}
                                                    className="border-gray-300 rounded"
                                                />
                                                <input
                                                    type="date"
                                                    value={mat.added_at}
                                                    onChange={(e) => {
                                                        const list = [...data.materials];
                                                        list[idx].added_at = e.target.value;
                                                        setData('materials', list);
                                                    }}
                                                    className="border-gray-300 rounded"
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => removeMaterial(idx)}
                                                    className="text-red-500"
                                                >
                                                    &times;
                                                </button>
                                            </div>
                                        </Fragment>
                                    ))}
                                    <button
                                        type="button"
                                        onClick={addMaterial}
                                        className="mt-2 text-blue-500"
                                    >
                                        + add material
                                    </button>
                                </div>

                                {/* workers section */}
                                <div className="mt-6">
                                    <h3 className="font-semibold">Workers</h3>
                                    {data.workers.map((w, idx) => (
                                        <Fragment key={idx}>
                                            <div className="grid grid-cols-5 gap-2 items-center mt-2">
                                                <select
                                                    name="id"
                                                    value={w.id}
                                                    onChange={(e) => {
                                                        const list = [...data.workers];
                                                        list[idx].id = e.target.value;
                                                        setData('workers', list);
                                                    }}
                                                    className="border-gray-300 rounded col-span-2"
                                                >
                                                    <option value="">--select--</option>
                                                    {workers.map((wk) => (
                                                        <option key={wk.id} value={wk.id}>
                                                            {wk.name}
                                                        </option>
                                                    ))}
                                                </select>
                                                <input
                                                    type="datetime-local"
                                                    value={w.start_time}
                                                    onChange={(e) => {
                                                        const list = [...data.workers];
                                                        list[idx].start_time = e.target.value;
                                                        setData('workers', list);
                                                    }}
                                                    className="border-gray-300 rounded"
                                                />
                                                <input
                                                    type="datetime-local"
                                                    value={w.end_time}
                                                    onChange={(e) => {
                                                        const list = [...data.workers];
                                                        list[idx].end_time = e.target.value;
                                                        setData('workers', list);
                                                    }}
                                                    className="border-gray-300 rounded"
                                                />
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    placeholder="amount"
                                                    value={w.amount}
                                                    onChange={(e) => {
                                                        const list = [...data.workers];
                                                        list[idx].amount = e.target.value;
                                                        setData('workers', list);
                                                    }}
                                                    className="border-gray-300 rounded"
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => removeWorker(idx)}
                                                    className="text-red-500"
                                                >
                                                    &times;
                                                </button>
                                            </div>
                                        </Fragment>
                                    ))}
                                    <button
                                        type="button"
                                        onClick={addWorker}
                                        className="mt-2 text-blue-500"
                                    >
                                        + add worker
                                    </button>
                                </div>

                                <div className="mt-6">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="px-4 py-2 bg-blue-600 text-white rounded"
                                    >
                                        Update
                                    </button>
                                    <Link
                                        href={route('slots.show', slot.id)}
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
