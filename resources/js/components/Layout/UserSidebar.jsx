import {useEffect} from "react";

export default function UserSidebar({ activeTab, setActiveTab }) {
    const menuItems = [
        { id: "borrowing", label: "Borrowing History" },
    ];

    useEffect(() => {
        if (!activeTab) {
            setActiveTab("borrowing");
        }
    }, [activeTab, setActiveTab]);

    return (
        <aside className="w-64 bg-neutral-900 text-neutral-300 border-r border-neutral-800 p-6 flex flex-col">
            <h2 className="text-xl font-semibold text-cyan-400 mb-8 tracking-tight">
                Vault Library
            </h2>

            <ul className="space-y-2">
                {menuItems.map((item) => (
                    <li
                        key={item.id}
                        onClick={() => setActiveTab(item.id)}
                        className={`cursor-pointer rounded-lg px-4 py-2 text-sm font-medium transition-all duration-300
                            ${
                            activeTab === item.id
                                ? "bg-gradient-to-r from-cyan-500 to-emerald-400 text-neutral-900 shadow-md"
                                : "hover:bg-neutral-800 hover:text-cyan-400"
                        }`}
                    >
                        {item.label}
                    </li>
                ))}
            </ul>
        </aside>
    );
}

